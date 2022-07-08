function markSource(ms, ta) {
  var ok = ta.checked;
  $.ajax({
    type: "POST",
    url: "/ajax/mark/?id=" + ms,
    data: { dd1: ms, dd2: ok },
  }).done(function (data) {
    console.log(data);
    $("label_select_source").html(data);
  });
}
