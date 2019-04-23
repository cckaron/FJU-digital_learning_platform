<div id="correctModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content" style="width: 400px">
            <form method="post" id="correct_form">
                <div class="modal-header">
                    <h4 class="modal-title">批改作業</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        &times;
                    </button>
                </div>
                <div class="modal-body">
                    {{ csrf_field() }}
                    <span id="form_output"></span>
                    <div class="form-group col-md-6">
                        <label>分數</label>
                        <input type="number" step="0.01" id="modal_score" name="score" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label>教師評語</label>
                        <textarea cols="40"id="modal_comment" rows="5" name="comment" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="student_assignment_id" id="student_assignment_id" value="" />
                    {{--<button type="button" class="btn btn-default" data-dismiss="modal" style="float: left;">關閉</button>--}}
                    <input type="submit" name="submit" id="action" value="確認" class="btn btn-info">
                </div>
            </form>
        </div>
    </div>
</div>