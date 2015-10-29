<script>

var area = new Morris.Line({
    element: 'weekly_sales_invoice_content',
    resize:true,
    data: <?php echo json_encode($sales_invoice_data); ?>,
    dateFormat:function(param){return new Date(param).format('F d, Y');},
    xLabelFormat:function(param){return new Date(param).format('M d');},
    xLabels:'day',
    xkey: 'date',
    ykeys: ['sales_invoice_amount'],
    labels: ['Total Amount'],
    lineColors: ['#3c8dbc'],
    hideHover: 'auto'
});
$('#weekly_sales_invoice_content').resize();
</script>