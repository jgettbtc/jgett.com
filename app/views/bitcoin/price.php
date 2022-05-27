<?php
function writePercentChangeSpan($model){
    if ($model->percent_change == 0)
        echo '<small class="text-muted">&#9650;';
    else if ($model->percent_change > 0)
        echo '<small class="text-success">&#9650;';
    else
        echo '<small class="text-danger">&#9660;';

    if ($model->historical['price'] == 0)
        echo "&#8734;%";
    else
        echo number_format($model->percent_change, 2).'%';

    echo '</small>';
}
?>

<?php $this->section('head')->begin(); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .datepicker {
        margin: 0 auto;
    }
</style>
<?php $this->section('head')->end(); ?>

<div>
    <div class="mb-3">
        <a href="<?=$this->url('home', 'index')?>">Home</a>
    </div>

    <h5>Bitcoin Price Lookup</h5>

	<hr>

    <form method="GET">
        <div class="row mb-2">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text">Date</span>
                    <input type="text" class="form-control price-date" placeholder="YYYY-MM-DD" name="d" value="<?=$model->date?>"  data-bs-toggle="modal" data-bs-target="#datepicker-modal">
                    <button class="btn btn-outline-primary ok-button" type="submit">OK</button>
                </div>
            </div>
        </div>
    </form>

    <div class="btn-group mb-3" role="group">
        <a href="<?=$this->url('bitcoin', 'price')?>" class="btn btn-outline-secondary">today</a>
        <a href="?d=<?=$model->prev_day?>" class="btn btn-outline-secondary">-1d</a>
        <a href="?d=<?=$model->next_day?>" class="btn btn-outline-secondary">+1d</a>
        <a href="?d=<?=$model->prev_month?>" class="btn btn-outline-secondary">-1m</a>
        <a href="?d=<?=$model->next_month?>" class="btn btn-outline-secondary">+1m</a>
        <a href="?d=<?=$model->prev_year?>" class="btn btn-outline-secondary">-1y</a>
        <a href="?d=<?=$model->next_year?>" class="btn btn-outline-secondary">+1y</a>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <?=$model->date?> Bitcoin Price:
            </h5>
            
            <hr>

            <h1 class="display-3 mb-4">
                <?php if ($model->historical['price'] == 0 && $model->date > date('Y-m-d')): ?>
                    $&#8734;/21M
                <?php else: ?>
                    $<?=number_format($model->historical['price'], 2)?>
                <?php endif ?>
            </h1>

            <hr>

            <h6 class="d-inline-block">Current:</h6>
            <h5 class="d-inline-block">
                $<?=number_format($model->current['price'], 2)?>
                <?php writePercentChangeSpan($model); ?>
            </h5>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="datepicker-modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <div class="datepicker"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-outline-primary modal-ok-button" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<?php $this->section('scripts')->begin(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.10.7/dayjs.min.js" integrity="sha512-bwD3VD/j6ypSSnyjuaURidZksoVx3L1RPvTkleC48SbHCZsemT3VKMD39KknPnH728LLXVMTisESIBOAb5/W0Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    console.log(""
        + "+----------------------------------------------------+\n"
        + "| bitcoin price lookup                               |\n"
        + "+----------------------------------------------------+\n"
        + "|   current price.........: <?=str_pad($model->current['price'], 25)?>|\n"
        + "|   current source........: <?=str_pad($model->current['source'], 25)?>|\n"
        + "|   current imported......: <?=str_pad($model->current['import_date'], 25)?>|\n"
        + "|   historical price......: <?=str_pad($model->historical['price'], 25)?>|\n"
        + "|   historical source.....: <?=str_pad($model->historical['source'], 25)?>|\n"
        + "|   historical imported...: <?=str_pad($model->historical['import_date'], 25)?>|\n"
        + "+----------------------------------------------------+\n"
    );

    var modal = $("#datepicker-modal");

    var dpick = $('.datepicker', modal).datepicker({
        format: "yyyy-mm-dd",
        defaultViewDate: $(".price-date").val()
    });

    modal.on('shown.bs.modal', function () {
        dpick.datepicker('setDate', $(".price-date").val());
    }).on('click', '.modal-ok-button', function () {
        var d = dpick.datepicker('getDate');
        var fd = dayjs(d).format('YYYY-MM-DD');
        $(".price-date").val(fd);
        $(".ok-button").focus();
        $("form").submit();
    });
    
</script>
<?php $this->section('scripts')->end(); ?>