<x-mail::message>
{{-- Header Customizado com Favicon --}}
<x-slot:header>
    <tr>
        <td class="header" style="text-align: center; padding: 25px 0;">
            <a href="{{ config('app.url') }}" style="display: inline-block;">
                <img src="{{ asset('favicon.png') }}" alt="{{ config('app.name') }}" style="height: 48px; width: auto;">
            </a>
        </td>
    </tr>
</x-slot:header>

{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Whoops!')
@else
# @lang('Hello!')
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<x-mail::button :url="$actionUrl" :color="$color">
{{ $actionText }}
</x-mail::button>
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Regards,')<br>
{{ config('app.name') }}
@endif

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
Se você estiver com dificuldades para clicar no botão "{{ $actionText }}", copie e cole o URL abaixo no seu navegador: <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>