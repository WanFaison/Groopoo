$(document).ready(function() {
    $('.datepicker').datepicker({
      format: 'dd/mm/yyyy', 
      startDate: '-3d' // Optional: restrict start date
    });
  });