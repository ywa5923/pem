$(document).ready(function () {
   $('.select-evaluation').click(function () {
      var route=$(this).data("evaluationuri");
      window.location.href=$(this).data("evaluationuri")+"?year="+$(this).html()
   })
});