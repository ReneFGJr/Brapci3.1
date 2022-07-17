function markSource(ms, ta) {
  var ok = ta.checked;
  $.ajax({
    type: "POST",
    url: "/ajax/mark/?time=" + Date.now() + "&id=" + ms,
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
