<div {{ $attributes->merge([
    'class' => 'dashboard-statistic window',
    'style' => $widgetData->clickRoute() ? 'cursor: pointer' : '',
    'onclick' => $widgetData->clickRoute() ? "location.href='{$widgetData->clickRoute()}'" : null,
]) }}>
    <img alt="Icon" class="statistic-icon" src="{{asset("images/" . $widgetData->iconPath())}}"/>

    <div class="statistic-text">
        <div class="statistic-title">{{ $widgetData->title() }}</div>
        <div class="statistic-number">{{ $widgetData->value() }}</div>
    </div>
</div>
