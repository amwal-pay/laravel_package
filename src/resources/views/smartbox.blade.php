<script>
    window.myAppData = {
        url: @json($url),
        jsonData: @json($jsonData),
        callback: @json($callback),
        cancel_url: @json($cancel_url)
    };
</script>

<script src="{{ asset('js/vendor/amwalpay/smartbox.js') }}"></script>