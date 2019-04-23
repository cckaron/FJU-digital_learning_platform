<div id="openMakeUpModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content" style="width: 600px">
            <form method="post" id="openMakeUp_form">
                <div class="modal-header">
                    <h4 class="modal-title">開放補繳作業</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        &times;
                    </button>
                </div>
                <div class="modal-body">
                    {{ csrf_field() }}
                    <span id="form_output_openMakeUp"></span>
                    <div class="form-group row">
                        <label class="col-md-2 m-t-10">截止時間</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control" id="datepicker-end" name="makeUpDate[]" placeholder="日期" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control" id="timepicker-end" name="makeUpDate[]" placeholder="時間" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fa fa-calendar-times"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="student_assignment_id" id="makeUpModal_student_assignment_id"/>
                    <input type="submit" name="submit" id="action" value="確認" class="btn btn-info">
                </div>
            </form>
        </div>
    </div>
</div>