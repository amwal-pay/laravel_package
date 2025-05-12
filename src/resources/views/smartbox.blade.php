<div id="loader"
    style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.9); z-index: 9999; display: flex; align-items: center; justify-content: center; flex-direction: column;">
    <div class="spinner"
        style="border: 5px solid rgba(0, 0, 0, 0.1); border-top: 5px solid #007bff; border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite;">
    </div>
    <p style="margin-top: 20px; font-size: 18px; color: #333; font-family: Arial, sans-serif;">Please wait, do not close
        the page...</p>
</div>

<style>
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
<script type='text/javascript'>

    // smartbox script
    var url = '<?php echo $url; ?>';
    // Adding the script tag to the head as suggested before
    var head = document.head;
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = url;
    // Fire the loading
    head.appendChild(script);

    setTimeout(function () {
        // Parse JSON string to JavaScript object
        var jsonData = '<?php echo $jsonData; ?>';
        var data = JSON.parse(jsonData);
        callSmartBox(data);
    }, 2000);

    var callBackStatus = "";

    function callSmartBox(data) {
        var loader = document.getElementById('loader');
        if (loader) {
            loader.style.display = 'none';
        }
        if (data["MID"] === "" || data["TID"] === "") {
            document.getElementById("Error").style.display = "block";
            return;
        }

        SmartBox.Checkout.configure = {
            ...data,

            completeCallback: function (data) {

                var callback = '<?php echo $callback; ?>';
                var dateResponse = data.data.data;
                window.location = callback + '?amount=' + dateResponse.amount + '&currencyId=' + dateResponse.currencyId + '&customerId=' + dateResponse.customerId + '&customerTokenId=' + dateResponse.customerTokenId + '&merchantReference=' + dateResponse.merchantReference + '&responseCode=' + data.data.responseCode + '&transactionId=' + dateResponse.transactionId + '&transactionTime=' + dateResponse.transactionTime + '&secureHashValue=' + dateResponse.secureHashValue;
            },
            errorCallback: function (data) {
                console.log("errorCallback Received Data", data);
                // window.location = cancel_url;
            },
            cancelCallback: function () {

            },
        };
        SmartBox.Checkout.showSmartBox()
    }

</script>