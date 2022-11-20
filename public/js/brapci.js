function markArticle(ms, ta) {
  var ok = ta.checked;
  $.ajax({
    type: "POST",
    url: "/ajax/work/?time=" + Date.now() + "&id=" + ms + "&check=" + ok,
  }).done(function (data) {
    $("#result").html(data);
    console.log(data);
  });
}

function markClear()
  {
  if (confirm("Clear All"))
  {
    $.ajax({
      type: "POST",
      url: "/ajax/work_clear/?time=" + Date.now(),
    }).done(function (data) {
      $("#result").html(data);
      console.log(data);
    });
  }
  }

function markAll($uri,$data)
  {
    $uri = $("#uri").val();
    $dta = $("#query").val();
    $.ajax({
      type: "POST",
      url: "/ajax/work_all?time=" + Date.now(),
      data: { dd1: $uri, dd2: $dta },
    }).done(function (data) {
      $("#result").html(data);
      console.log(data);
    });
  }

function markSource(ms, ta) {
  var ok = ta.checked;
  $.ajax({
    type: "POST",
    url: "/ajax/mark?time=" + Date.now() + "&id=" + ms,
    data: { dd1: ms, dd2: ok },
  }).done(function (data) {
    $("#label_select_source").html(data);
    console.log(data);
  });
}

function download($url) {
  NewWindow = window.open(
    $url,
    "newwin2",
    "scrollbars=yes,resizable=no,width=800,height=800,top=10,left=10"
  );
  NewWindow.focus();
  void 0;
}

function winclose() {
  close();
}

function wclose() {
  window.opener.location.reload();
  close();
}

function reload()
  {
    location.reload();
  }

  function bug_report($this)
    {
        $check = $("#ebug").val();
        alert('===>'+$check);
        $idc = $("#idc").val();
        $("#bug").load("/ajax/bug/report/?id=" + $idc + "&type="+$check);
        alert('Obrigado, o erro foi reportado');
    }
