<!-- Highcharts -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/wordcloud.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<style>
    body {
        background: #f4f6fa;
    }

    #container {
        background: #fff;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, .08);
        height: 520px;
    }
</style>

<div class="content my-5">
    <div id="container"></div>
</div>

<script>
    Highcharts.chart('container', {
        chart: {
            type: 'wordcloud',
            backgroundColor: 'transparent'
        },

        title: {
            text: null
        },

        tooltip: {
            pointFormat: '<b>{point.name}</b>: {point.weight} ocorrências'
        },

        series: [{
            name: 'Frequência',
            data: <?= json_encode($data, JSON_UNESCAPED_UNICODE) ?>,
            rotation: {
                from: 0,
                to: 0
            },
            spiral: 'rectangular',
            minFontSize: 14,
            maxFontSize: 56
        }],

        credits: {
            enabled: false
        }
    });
</script>