<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminReportsTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD = 'a-long-test-password';

    private function maintainer(): User
    {
        return User::factory()->create(['password' => Hash::make(self::PASSWORD)]);
    }

    private function report(array $attributes = []): Report
    {
        return Report::factory()
            ->for(Facility::factory()->create())
            ->create(array_merge([
                'message' => 'The phone number has changed.',
                'contact' => null,
                'status' => Report::STATUS_NEW,
                'resolved_at' => null,
            ], $attributes));
    }

    public function test_guests_are_sent_to_the_sign_in_page(): void
    {
        $report = $this->report();

        $this->get(route('admin.reports.index'))->assertRedirect(route('login'));
        $this->get(route('admin.reports.show', $report))->assertRedirect(route('login'));
        $this->post(route('admin.reports.resolve', $report))->assertRedirect(route('login'));

        $this->assertSame(Report::STATUS_NEW, $report->fresh()->status);
    }

    public function test_the_sign_in_page_loads_and_is_kept_out_of_search_engines_and_caches(): void
    {
        $response = $this->get(route('login'))->assertOk();

        $response->assertSee(__('admin.sign_in'));
        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_a_maintainer_can_sign_in(): void
    {
        $user = $this->maintainer();

        $this->post(route('admin.login.store'), ['email' => $user->email, 'password' => self::PASSWORD])
            ->assertRedirect(route('admin.reports.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_details_do_not_sign_in_and_the_password_is_not_sent_back(): void
    {
        $user = $this->maintainer();

        $this->from(route('login'))
            ->post(route('admin.login.store'), ['email' => $user->email, 'password' => 'not-the-password'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email')
            ->assertSessionHasInput('email', $user->email)
            ->assertSessionMissing('_old_input.password');

        $this->assertGuest();
    }

    public function test_non_text_sign_in_values_are_rejected_without_errors(): void
    {
        $this->post(route('admin.login.store'), ['email' => ['x'], 'password' => ['y']])
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }

    public function test_sign_in_attempts_are_rate_limited(): void
    {
        $user = $this->maintainer();

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('admin.login.store'), ['email' => $user->email, 'password' => 'wrong-'.$i]);
        }

        $this->post(route('admin.login.store'), ['email' => $user->email, 'password' => self::PASSWORD])
            ->assertStatus(429);

        $this->assertGuest();
    }

    public function test_a_maintainer_can_sign_out(): void
    {
        $this->actingAs($this->maintainer())
            ->post(route('admin.logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_the_list_shows_new_reports_by_default(): void
    {
        $this->report(['message' => 'Alpha report still waiting']);
        $this->report(['message' => 'Beta report already done', 'status' => Report::STATUS_RESOLVED, 'resolved_at' => now()]);

        $this->actingAs($this->maintainer())
            ->get(route('admin.reports.index'))
            ->assertOk()
            ->assertSee('Alpha report still waiting')
            ->assertDontSee('Beta report already done');
    }

    public function test_the_list_can_show_resolved_or_all_reports(): void
    {
        $this->report(['message' => 'Alpha report still waiting']);
        $this->report(['message' => 'Beta report already done', 'status' => Report::STATUS_RESOLVED, 'resolved_at' => now()]);
        $user = $this->maintainer();

        $this->actingAs($user)->get(route('admin.reports.index', ['status' => 'resolved']))
            ->assertSee('Beta report already done')
            ->assertDontSee('Alpha report still waiting');

        $this->actingAs($user)->get(route('admin.reports.index', ['status' => 'all']))
            ->assertSee('Alpha report still waiting')
            ->assertSee('Beta report already done');
    }

    public function test_unknown_or_non_text_status_values_fall_back_to_new_reports(): void
    {
        $this->report(['message' => 'Alpha report still waiting']);
        $user = $this->maintainer();

        $this->actingAs($user)->get('/admin/reports?status=nonsense')
            ->assertOk()->assertSee('Alpha report still waiting');

        $this->actingAs($user)->get('/admin/reports?status[]=x')
            ->assertOk()->assertSee('Alpha report still waiting');
    }

    public function test_the_list_says_so_when_there_are_no_reports(): void
    {
        $this->actingAs($this->maintainer())
            ->get(route('admin.reports.index'))
            ->assertOk()
            ->assertSee(__('admin.none'));
    }

    public function test_the_list_is_paginated_with_20_per_page(): void
    {
        for ($i = 1; $i <= 21; $i++) {
            $this->report(['message' => sprintf('Queue item %02d', $i)]);
        }
        $user = $this->maintainer();

        $this->actingAs($user)->get(route('admin.reports.index'))
            ->assertSee('Queue item 20')
            ->assertDontSee('Queue item 21')
            ->assertSee(__('admin.next'));

        $this->actingAs($user)->get(route('admin.reports.index', ['page' => 2]))
            ->assertSee('Queue item 21')
            ->assertSee(__('admin.previous'));
    }

    public function test_the_list_does_not_show_contact_details_but_the_report_page_does(): void
    {
        $report = $this->report(['contact' => '0700 000 111']);
        $user = $this->maintainer();

        $this->actingAs($user)->get(route('admin.reports.index'))
            ->assertOk()
            ->assertDontSee('0700 000 111')
            ->assertSee(__('admin.contact_given'));

        $this->actingAs($user)->get(route('admin.reports.show', $report))
            ->assertOk()
            ->assertSee('0700 000 111');
    }

    public function test_visitor_text_is_escaped(): void
    {
        $report = $this->report(['message' => '<script>alert(1)</script> wrong phone number']);

        $this->actingAs($this->maintainer())
            ->get(route('admin.reports.show', $report))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_an_unknown_report_returns_404(): void
    {
        $this->actingAs($this->maintainer())
            ->get(route('admin.reports.show', 9999))
            ->assertNotFound();
    }

    public function test_a_maintainer_can_resolve_a_report(): void
    {
        $report = $this->report();

        $this->actingAs($this->maintainer())
            ->post(route('admin.reports.resolve', $report))
            ->assertRedirect(route('admin.reports.index'))
            ->assertSessionHas('admin_status', __('admin.resolved_ok'));

        $report->refresh();
        $this->assertSame(Report::STATUS_RESOLVED, $report->status);
        $this->assertNotNull($report->resolved_at);
    }

    public function test_resolving_twice_keeps_the_original_resolution_date(): void
    {
        $original = now()->subDays(10)->setMicroseconds(0);
        $report = $this->report(['status' => Report::STATUS_RESOLVED, 'resolved_at' => $original]);

        $this->actingAs($this->maintainer())
            ->post(route('admin.reports.resolve', $report))
            ->assertRedirect(route('admin.reports.show', $report))
            ->assertSessionHas('admin_status', __('admin.already_resolved'));

        $this->assertTrue($report->fresh()->resolved_at->equalTo($original));
    }

    public function test_admin_pages_are_kept_out_of_search_engines_and_caches(): void
    {
        $response = $this->actingAs($this->maintainer())->get(route('admin.reports.index'))->assertOk();

        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_public_pages_do_not_link_to_the_admin_area(): void
    {
        $this->get(route('home'))->assertOk()->assertDontSee('/admin', false);
    }

    public function test_the_create_admin_command_makes_a_maintainer_who_can_sign_in(): void
    {
        $this->artisan('admin:create', ['name' => 'Test Admin', 'email' => 'Admin@Example.org'])
            ->expectsQuestion('Password (at least 12 characters)', 'correct-horse-battery')
            ->expectsQuestion('Confirm password', 'correct-horse-battery')
            ->assertExitCode(0);

        $user = User::where('email', 'admin@example.org')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('correct-horse-battery', $user->password));
    }

    public function test_the_create_admin_command_refuses_a_short_password(): void
    {
        $this->artisan('admin:create', ['name' => 'Test Admin', 'email' => 'admin@example.org'])
            ->expectsQuestion('Password (at least 12 characters)', 'short')
            ->assertExitCode(1);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_the_create_admin_command_refuses_mismatched_passwords(): void
    {
        $this->artisan('admin:create', ['name' => 'Test Admin', 'email' => 'admin@example.org'])
            ->expectsQuestion('Password (at least 12 characters)', 'correct-horse-battery')
            ->expectsQuestion('Confirm password', 'something-different-here')
            ->assertExitCode(1);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_the_create_admin_command_refuses_a_bad_or_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.org']);

        $this->artisan('admin:create', ['name' => 'Test Admin', 'email' => 'taken@example.org'])->assertExitCode(1);
        $this->artisan('admin:create', ['name' => 'Test Admin', 'email' => 'not-an-email'])->assertExitCode(1);

        $this->assertDatabaseCount('users', 1);
    }
}
