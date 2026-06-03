@props([
    'text',
    'position' => 'top'
])

<div class="tooltip-wrapper" 
     x-data="{ showTooltip: false }" 
     @mouseenter="showTooltip = true" 
     @mouseleave="showTooltip = false"
     style="position: relative; display: inline-flex;">
    
    {{ $slot }}
    
    <div class="tooltip-content tooltip-{{ $position }}"
         x-show="showTooltip"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display: none;">
        {{ $text }}
        <div class="tooltip-arrow"></div>
    </div>
</div>

<style>
.tooltip-wrapper {
    position: relative;
    display: inline-flex;
}

.tooltip-content {
    position: absolute;
    background-color: var(--color-primary-900, #0F1E32);
    color: #ffffff;
    font-size: 11px;
    font-weight: 500;
    padding: 6px 12px;
    border-radius: var(--radius-sm, 4px);
    white-space: nowrap;
    z-index: 1050;
    pointer-events: none;
    box-shadow: var(--shadow-md, 0 4px 12px rgba(0,0,0,0.15));
}

.tooltip-arrow {
    position: absolute;
    width: 0;
    height: 0;
    border-style: solid;
}

/* Tooltip Positions */
.tooltip-top {
    bottom: 100%;
    left: 50%;
    transform: translate(-50%, -8px);
}
.tooltip-top .tooltip-arrow {
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border-width: 6px 6px 0 6px;
    border-color: var(--color-primary-900, #0F1E32) transparent transparent transparent;
}

.tooltip-bottom {
    top: 100%;
    left: 50%;
    transform: translate(-50%, 8px);
}
.tooltip-bottom .tooltip-arrow {
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    border-width: 0 6px 6px 6px;
    border-color: transparent transparent var(--color-primary-900, #0F1E32) transparent;
}

.tooltip-left {
    right: 100%;
    top: 50%;
    transform: translate(-8px, -50%);
}
.tooltip-left .tooltip-arrow {
    left: 100%;
    top: 50%;
    transform: translateY(-50%);
    border-width: 6px 0 6px 6px;
    border-color: transparent transparent transparent var(--color-primary-900, #0F1E32);
}

.tooltip-right {
    left: 100%;
    top: 50%;
    transform: translate(8px, -50%);
}
.tooltip-right .tooltip-arrow {
    right: 100%;
    top: 50%;
    transform: translateY(-50%);
    border-width: 6px 6px 6px 0;
    border-color: transparent var(--color-primary-900, #0F1E32) transparent transparent;
}
</style>
