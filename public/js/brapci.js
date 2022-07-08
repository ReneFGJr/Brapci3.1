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
