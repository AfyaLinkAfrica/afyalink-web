<div class="badges">
    <span class="badge">{{ __('facilities.types.'.$facility->type) }}</span>
    @if ($facility->source === 'synthetic')
        <span class="badge warn">{{ __('facilities.demo_badge') }}</span>
    @endif
    @if ($facility->isVerified())
        <span class="badge">{{ __('facilities.verified_on', ['date' => $facility->verified_at->translatedFormat('j F Y')]) }}</span>
    @else
        <span class="badge warn">{{ __('facilities.not_verified') }}</span>
    @endif
</div>
