<?php
//UA-12713129-1 - Brapci.inf.br
//G-B720HV20XK - Benancib
?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= $GOOGLEID; ?>"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', '<?= $GOOGLEID; ?>');
</script>