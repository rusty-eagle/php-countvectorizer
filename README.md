# php-countvectorizer

This will allow you to turn text into input data for php-ml's KNearestNeighbors.

You can first use the fit_transform() function to convert the data.  You can use that as the input for php-ml's train method.

Once you have a trained model, you can use the test_fit_transform() to convert the new data you want a prediction on.  Then you that as the input for predict().
