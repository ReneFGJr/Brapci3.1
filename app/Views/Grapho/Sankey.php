<script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-core.min.js"></script>
<script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-sankey.min.js"></script>
<style type="text/css">
    html,
    body,
    #container {
        width: 100%;
        height: 400px;
        margin: 0;
        padding: 0;
        font-size: 0.2em;
    }
</style>



<div id="container"></div>
<script>
    anychart.onDocumentReady(function() {

        // add data
        const data = [{
                from: "(1994)GT",
                to: "(1995)GT1",
                value: 11
            },
            {
                from: "(1994)GT",
                to: "(1995)GT2",
                value: 12
            },
            {
                from: "(1994)GT",
                to: "(1995)GT3",
                value: 6
            },
            {
                from: "(1994)GT",
                to: "(1995)GT4",
                value: 11
            },
            {
                from: "(1994)GT",
                to: "(1995)GT5",
                value: 10
            },
            {
                from: "(1994)GT",
                to: "(1995)GT6",
                value: 6
            },

            /******************** 1997 */
            {
                from: "(1995)GT1",
                to: "(1997)GT1",
                value: 29
            },
            {
                from: "(1995)GT2",
                to: "(1997)GT2",
                value: 32
            },
            {
                from: "(1995)GT3",
                to: "(1997)GT3",
                value: 9
            },
            {
                from: "(1995)GT4",
                to: "(1997)GT4",
                value: 34
            },
            {
                from: "(1995)GT5",
                to: "(1997)GT5",
                value: 21
            },
            {
                from: "(1995)GT6",
                to: "(1997)GT6",
                value: 9
            },
            /******************** 2000 */
            {
                from: "(1997)GT1",
                to: "(2000)GT1",
                value: 23
            },
            {
                from: "(1997)GT2",
                to: "(2000)GT2",
                value: 23
            },
            {
                from: "(1997)GT3",
                to: "(2000)GT3",
                value: 48
            },
            {
                from: "(1997)GT4",
                to: "(2000)GT4",
                value: 43
            },
            {
                from: "(1997)GT5",
                to: "(2000)GT5",
                value: 29
            },
            {
                from: "(1997)GT6",
                to: "(2000)GT6",
                value: 15
            },
            {
                from: "(1997)GT1",
                to: "(2000)GT7",
                value: 18
            },
            {
                from: "(1997)GT2",
                to: "(2000)GT8",
                value: 14
            },

            /******************************* 2006 */
            {
                from: "(2000)GT8",
                to: "(2006)GT1",
                value: 13
            },
            {
                from: "(2000)GT2",
                to: "(2006)GT2",
                value: 23
            },
            {
                from: "(2000)GT4",
                to: "(2006)GT3",
                value: 21
            },
            {
                from: "(2000)GT7",
                to: "(2006)GT4",
                value: 19
            },
            {
                from: "(2000)GT6",
                to: "(2006)GT5",
                value: 8
            },
            {
                from: "(2000)GT3",
                to: "(2006)GT7",
                value: 6
            },
            {
                from: "(2000)GT5",
                to: "(2006)GT7",
                value: 7
            },
            {
                from: "(2000)GT5",
                to: "(2006)GT7",
                value: 7
            },
            {
                from: "(2000)GT5",
                to: "(2006)GT7",
                value: 7
            },
            {
                from: "(2000)GT8",
                to: "(2006)GT1",
                value: 13
            },
            {
                from: "(2000)GT1",
                to: "(2006)GT7",
                value: 13
            },
            {
                from: "(2000)GT6",
                to: "(2006)GT6",
                value: 7
            },
            /******************* 2008 */
            {
                from: "(2006)GT1",
                to: "(2008)GT1",
                value: 21
            },
            {
                from: "(2006)GT2",
                to: "(2008)GT2",
                value: 23
            },
            {
                from: "(2006)GT3",
                to: "(2008)GT3",
                value: 20
            },
            {
                from: "(2006)GT4",
                to: "(2008)GT4",
                value: 16
            }, {
                from: "(2006)GT5",
                to: "(2008)GT5",
                value: 23
            }, {
                from: "(2006)GT6",
                to: "(2008)GT6",
                value: 18
            },
            {
                from: "(2006)GT7",
                to: "(2008)GT7",
                value: 13
            },
            {
                from: "(2006)GT7",
                to: "(2008)GT8",
                value: 16
            },

            /******************* 2010 */
            {
                from: "(2008)GT1",
                to: "(2010)GT1",
                value: 19
            },
            {
                from: "(2008)GT2",
                to: "(2010)GT2",
                value: 27
            },
            {
                from: "(2008)GT2",
                to: "(2010)GT2",
                value: 27
            },
            {
                from: "(2008)GT3",
                to: "(2010)GT3",
                value: 19
            },
            {
                from: "(2008)GT4",
                to: "(2010)GT4",
                value: 40
            },
            {
                from: "(2008)GT5",
                to: "(2010)GT5",
                value: 20
            },
            {
                from: "(2008)GT6",
                to: "(2010)GT6",
                value: 20
            },
            {
                from: "(2008)GT8",
                to: "(2010)GT8",
                value: 27
            },
            {
                from: "(2008)GT7",
                to: "(2010)GT7",
                value: 25
            },
            {
                from: "(2008)GT2",
                to: "(2010)GT9",
                value: 3
            },
            {
                from: "(2008)GT3",
                to: "(2010)GT9",
                value: 3
            },
            {
                from: "(2008)GT1",
                to: "(2010)GT9",
                value: 3
            },
            {
                from: "(2008)GT1",
                to: "(2010)GT10",
                value: 29
            },
            {
                from: "(2008)GT2",
                to: "(2010)GT10",
                value: 6
            },
            {
                from: "(2008)GT3",
                to: "(2010)GT10",
                value: 8
            },

            /******************* 2011 */
            {
                from: "(2010)GT1",
                to: "(2011)GT1",
                value: 22
            },
            {
                from: "(2010)GT2",
                to: "(2011)GT2",
                value: 34
            },
            {
                from: "(2010)GT3",
                to: "(2011)GT3",
                value: 24
            },
            {
                from: "(2010)GT4",
                to: "(2011)GT4",
                value: 23
            },
            {
                from: "(2010)GT5",
                to: "(2011)GT5",
                value: 25
            },
            {
                from: "(2010)GT6",
                to: "(2011)GT6",
                value: 16
            },
            {
                from: "(2010)GT7",
                to: "(2011)GT7",
                value: 28
            },
            {
                from: "(2010)GT8",
                to: "(2011)GT8",
                value: 20
            },
            {
                from: "(2010)GT9",
                to: "(2011)GT9",
                value: 17
            },
            {
                from: "(2010)GT10",
                to: "(2011)GT10",
                value: 34
            },
            {
                from: "(2010)GT7",
                to: "(2011)GT11",
                value: 6
            },
            {
                from: "(2010)GT8",
                to: "(2011)GT11",
                value: 6
            },

            /******************* 2012 */
            {
                from: "(2011)GT1",
                to: "(2012)GT1",
                value: 27
            },
            {
                from: "(2011)GT2",
                to: "(2012)GT2",
                value: 33
            },
            {
                from: "(2011)GT3",
                to: "(2012)GT3",
                value: 34
            },
            {
                from: "(2011)GT4",
                to: "(2012)GT4",
                value: 24
            },
            {
                from: "(2011)GT5",
                to: "(2012)GT5",
                value: 31
            },
            {
                from: "(2011)GT6",
                to: "(2012)GT6",
                value: 21
            },
            {
                from: "(2011)GT7",
                to: "(2012)GT7",
                value: 30
            },
            {
                from: "(2011)GT8",
                to: "(2012)GT8",
                value: 30
            },
            {
                from: "(2011)GT9",
                to: "(2012)GT9",
                value: 22
            },
            {
                from: "(2011)GT10",
                to: "(2012)GT10",
                value: 38
            },
            {
                from: "(2011)GT11",
                to: "(2012)GT11",
                value: 23
            },
            /******************* 2013 */
            {
                from: "(2012)GT1",
                to: "(2013)GT1",
                value: 25
            },
            {
                from: "(2012)GT2",
                to: "(2013)GT2",
                value: 40
            },
            {
                from: "(2012)GT3",
                to: "(2013)GT3",
                value: 32
            },
            {
                from: "(2012)GT4",
                to: "(2013)GT4",
                value: 33
            },
            {
                from: "(2012)GT5",
                to: "(2013)GT5",
                value: 25
            },
            {
                from: "(2012)GT6",
                to: "(2013)GT6",
                value: 17
            },
            {
                from: "(2012)GT7",
                to: "(2013)GT7",
                value: 37
            },
            {
                from: "(2012)GT8",
                to: "(2013)GT8",
                value: 40
            },
            {
                from: "(2012)GT9",
                to: "(2013)GT9",
                value: 20
            },
            {
                from: "(2012)GT10",
                to: "(2013)GT10",
                value: 27
            },
            {
                from: "(2012)GT11",
                to: "(2013)GT11",
                value: 20
            },

            /******************* 2014 */
            {
                from: "(2013)GT1",
                to: "(2014)GT1",
                value: 19
            },
            {
                from: "(2013)GT2",
                to: "(2014)GT2",
                value: 52
            },
            {
                from: "(2013)GT3",
                to: "(2014)GT3",
                value: 22
            },
            {
                from: "(2013)GT4",
                to: "(2014)GT4",
                value: 37
            },
            {
                from: "(2013)GT5",
                to: "(2014)GT5",
                value: 29
            },
            {
                from: "(2013)GT6",
                to: "(2014)GT6",
                value: 23
            },
            {
                from: "(2013)GT7",
                to: "(2014)GT7",
                value: 37
            },
            {
                from: "(2013)GT8",
                to: "(2014)GT8",
                value: 35
            },
            {
                from: "(2013)GT9",
                to: "(2014)GT9",
                value: 23
            },
            {
                from: "(2013)GT10",
                to: "(2014)GT10",
                value: 35
            },
            {
                from: "(2013)GT11",
                to: "(2014)GT11",
                value: 11
            },

            /******************* 2015 */
            {
                from: "(2014)GT1",
                to: "(2015)GT1",
                value: 28
            },
            {
                from: "(2014)GT2",
                to: "(2015)GT2",
                value: 61
            },
            {
                from: "(2014)GT3",
                to: "(2015)GT3",
                value: 28
            },
            {
                from: "(2014)GT4",
                to: "(2015)GT4",
                value: 44
            },
            {
                from: "(2014)GT5",
                to: "(2015)GT5",
                value: 23
            },
            {
                from: "(2014)GT6",
                to: "(2015)GT6",
                value: 21
            },
            {
                from: "(2014)GT7",
                to: "(2015)GT7",
                value: 47
            },
            {
                from: "(2014)GT8",
                to: "(2015)GT8",
                value: 43
            },
            {
                from: "(2014)GT9",
                to: "(2015)GT9",
                value: 20
            },
            {
                from: "(2014)GT10",
                to: "(2015)GT10",
                value: 53
            },
            {
                from: "(2014)GT11",
                to: "(2015)GT11",
                value: 19
            },

            /******************* 2017 */
            {
                from: "(2015)GT1",
                to: "(2016)GT1",
                value: 28
            },
            {
                from: "(2015)GT2",
                to: "(2016)GT2",
                value: 61
            },
            {
                from: "(2015)GT3",
                to: "(2016)GT3",
                value: 28
            },
            {
                from: "(2015)GT4",
                to: "(2016)GT4",
                value: 44
            },
            {
                from: "(2015)GT5",
                to: "(2016)GT5",
                value: 23
            },
            {
                from: "(2015)GT6",
                to: "(2016)GT6",
                value: 21
            },
            {
                from: "(2015)GT7",
                to: "(2016)GT7",
                value: 47
            },
            {
                from: "(2015)GT8",
                to: "(2016)GT8",
                value: 43
            },
            {
                from: "(2015)GT9",
                to: "(2016)GT9",
                value: 20
            },
            {
                from: "(2015)GT10",
                to: "(2016)GT10",
                value: 53
            },
            {
                from: "(2015)GT11",
                to: "(2016)GT11",
                value: 19
            },

            /******************* 2017 */
            {
                from: "(2016)GT1",
                to: "(2017)GT1",
                value: 27
            },
            {
                from: "(2016)GT2",
                to: "(2017)GT2",
                value: 67
            },
            {
                from: "(2016)GT3",
                to: "(2017)GT3",
                value: 41
            },
            {
                from: "(2016)GT4",
                to: "(2017)GT4",
                value: 44
            },
            {
                from: "(2016)GT5",
                to: "(2017)GT5",
                value: 31
            },
            {
                from: "(2016)GT6",
                to: "(2017)GT6",
                value: 22
            },
            {
                from: "(2016)GT7",
                to: "(2017)GT7",
                value: 63
            },
            {
                from: "(2016)GT8",
                to: "(2017)GT8",
                value: 43
            },
            {
                from: "(2016)GT9",
                to: "(2017)GT9",
                value: 19
            },
            {
                from: "(2016)GT10",
                to: "(2017)GT10",
                value: 27
            },
            {
                from: "(2016)GT11",
                to: "(2017)GT11",
                value: 16
            },

            /******************* 2018 */
            {
                from: "(2017)GT1",
                to: "(2018)GT1",
                value: 22
            },
            {
                from: "(2017)GT2",
                to: "(2018)GT2",
                value: 50
            },
            {
                from: "(2017)GT3",
                to: "(2018)GT3",
                value: 50
            },
            {
                from: "(2017)GT4",
                to: "(2018)GT4",
                value: 52
            },
            {
                from: "(2017)GT5",
                to: "(2018)GT5",
                value: 45
            },
            {
                from: "(2017)GT6",
                to: "(2018)GT6",
                value: 25
            },
            {
                from: "(2017)GT7",
                to: "(2018)GT7",
                value: 37
            },
            {
                from: "(2017)GT8",
                to: "(2018)GT8",
                value: 48
            },
            {
                from: "(2017)GT9",
                to: "(2018)GT9",
                value: 31
            },
            {
                from: "(2017)GT10",
                to: "(2018)GT10",
                value: 45
            },
            {
                from: "(2017)GT11",
                to: "(2018)GT11",
                value: 12
            },

            /******************* 2019 */
            {
                from: "(2018)GT1",
                to: "(2019)GT1",
                value: 29
            },
            {
                from: "(2018)GT2",
                to: "(2019)GT2",
                value: 61
            },
            {
                from: "(2018)GT3",
                to: "(2019)GT3",
                value: 48
            },
            {
                from: "(2018)GT4",
                to: "(2019)GT4",
                value: 82
            },
            {
                from: "(2018)GT5",
                to: "(2019)GT5",
                value: 43
            },
            {
                from: "(2018)GT6",
                to: "(2019)GT6",
                value: 50
            },
            {
                from: "(2018)GT7",
                to: "(2019)GT7",
                value: 41
            },
            {
                from: "(2018)GT8",
                to: "(2019)GT8",
                value: 48
            },
            {
                from: "(2018)GT9",
                to: "(2019)GT9",
                value: 35
            },
            {
                from: "(2018)GT10",
                to: "(2019)GT10",
                value: 35
            },
            {
                from: "(2018)GT11",
                to: "(2019)GT11",
                value: 23
            },
            /******************* 2021 */
            {
                from: "(2019)GT1",
                to: "(2021)GT1",
                value: 19
            },
            {
                from: "(2019)GT2",
                to: "(2021)GT2",
                value: 38
            },
            {
                from: "(2019)GT3",
                to: "(2021)GT3",
                value: 38
            },
            {
                from: "(2019)GT4",
                to: "(2021)GT4",
                value: 49
            },
            {
                from: "(2019)GT5",
                to: "(2021)GT5",
                value: 41
            },
            {
                from: "(2019)GT6",
                to: "(2021)GT6",
                value: 49
            },
            {
                from: "(2019)GT7",
                to: "(2021)GT7",
                value: 26
            },
            {
                from: "(2019)GT8",
                to: "(2021)GT8",
                value: 43
            },
            {
                from: "(2019)GT9",
                to: "(2021)GT9",
                value: 15
            },
            {
                from: "(2019)GT10",
                to: "(2021)GT10",
                value: 25
            },
            {
                from: "(2019)GT11",
                to: "(2021)GT11",
                value: 15
            },

        ];

        // create a sankey diagram instance
        let chart = anychart.sankey();

        // load the data to the sankey diagram instance
        chart.data(data);

        // set the chart's padding
        chart.padding(20, 40);

        // add a title
        chart.title('GTs do Enancib por ano');

        // set the chart container id
        chart.container("container");

        // draw the chart
        chart.draw();

    });
</script>