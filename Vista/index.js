function goBack() {
    history.back();
}
function exportChart() {
    var chartContainer = document.querySelector('.chart-container');
    html2canvas(chartContainer).then(function(canvas) {
        var downloadLink = document.createElement('a');
        downloadLink.href = canvas.toDataURL(
            'image/png');
        downloadLink.download = 'Deserción_año.png';
        downloadLink.click();
    });
}