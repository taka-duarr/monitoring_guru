<div id="toast-container" style="position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 12px; max-width: 380px; width: calc(100% - 48px); pointer-events: none;"></div>

@if(session()->has('toast'))
    @php
        $toast = session('toast');
        $type = $toast['type'] ?? 'success';
        $message = $toast['message'] ?? '';
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.SimguruToast.show('{{ $type }}', '{!! addslashes($message) !!}');
        });
    </script>
@endif

{{-- Automatically bridge standard success flash to SimguruToast for consistency --}}
@if(session()->has('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.SimguruToast.show('success', '{!! addslashes(session('success')) !!}');
        });
    </script>
@endif

{{-- Automatically bridge standard error flash to SimguruToast for consistency --}}
@if(session()->has('error'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.SimguruToast.show('danger', '{!! addslashes(session('error')) !!}');
        });
    </script>
@endif
