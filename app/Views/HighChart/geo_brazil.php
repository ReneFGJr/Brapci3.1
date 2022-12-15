<?php
global $header_highchart;
if (!isset($header_highchart))
    {
        $header_highchart = 1;
        require('header.php');
    }
?>
<div id="go_brazil"></div>
<style>
    #container {
        height: 500px;
        min-width: 310px;
        max-width: 800px;
        margin: 0 auto;
    }

    .loading {
        margin-top: 10em;
        text-align: center;
        color: gray;
    }
</style>
<script>
    (async () => {

        const topology = await fetch(
            'https://code.highcharts.com/mapdata/countries/br/br-all.topo.json'
        ).then(response => response.json());

        // Prepare demo data. The data is joined to map using value of 'hc-key'
        // property by default. See API docs for 'joinBy' for more info on linking
        // data and map.
        const data = [ {$data} ];

        // Create the chart
        Highcharts.mapChart('go_brazil', {
            chart: {
                map: topology
            },

            title: {
                text: '<?=$title;?>'
            },

            subtitle: {
                text: 'Source map: <a href="http://code.highcharts.com/mapdata/countries/br/br-all.topo.json">Brazil</a>'
            },

            mapNavigation: {
                enabled: true,
                buttonOptions: {
                    verticalAlign: 'bottom'
                }
            },

            colorAxis: {
                min: 0
            },

            series: [{
                data: data,
                name: 'Research Data',
                states: {
                    hover: {
                        color: '#BADA55'
                    }
                },
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }]
        });

    })();
</script>