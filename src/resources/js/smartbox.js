if (window.myAppData.jsonData && window.myAppData.callback) {

    // Parse JSON string to JavaScript object
    var jsonData = window.myAppData.jsonData;
    var data = JSON.parse(jsonData);
    callSmartBox(data);

    function callSmartBox(data) {
        
        if (data["MID"] === "" || data["TID"] === "") {
            alert('Please add the correct configurations.');
            return;
        }

        SmartBox.Checkout.configure = {
            ...data,

            completeCallback: function (data) {

                var callback = window.myAppData.callback;
                var dateResponse = data.data.data;
                window.location = callback + '?amount=' + dateResponse.amount + '&currencyId=' + dateResponse.currencyId + '&customerId=' + dateResponse.customerId + '&customerTokenId=' + dateResponse.customerTokenId + '&merchantReference=' + dateResponse.merchantReference + '&responseCode=' + data.data.responseCode + '&transactionId=' + dateResponse.transactionId + '&transactionTime=' + dateResponse.transactionTime + '&secureHashValue=' + dateResponse.secureHashValue;
            },
            errorCallback: function (data) {
                console.log("errorCallback Received Data", data);
            },
            cancelCallback: function () {
                var cancel_url = window.myAppData.cancel_url;
                window.location = cancel_url;
            },
        };
        SmartBox.Checkout.showSmartBox()
    }

} else {
    alert('Please add the correct configurations.');
}