<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    @yield('content')
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    function closeWebView() {
        // This function will be called to close the webview
        if (window.ReactNativeWebView) {
            // For React Native WebView
            window.ReactNativeWebView.postMessage('closeWebView');
        } else if (window.webkit && window.webkit.messageHandlers) {
            // For iOS WKWebView
            window.webkit.messageHandlers.closeWebView.postMessage(null);
        } else if (window.CloseWebView) {
            // For Flutter WebView
            window.CloseWebView.postMessage('closeWebView');
        } else {
            window.close();
        }
    }
</script>
</body>
</html>
