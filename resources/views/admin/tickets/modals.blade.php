{{-- APPROVE MODAL --}}
<div id="approveModal"
     class="admin-ticket-modal-backdrop fixed inset-0 bg-black/70 hidden items-center justify-center z-50 backdrop-blur-sm"
     style="display: none;">
    <div class="admin-ticket-modal">
        <div class="admin-ticket-modal-head">
            <div>
                <h3>Approve Request</h3>
                <p>Optionally assign a staff member now, or assign later from the ticket view.</p>
            </div>
            <button type="button" onclick="closeApproveModal()" aria-label="Close approve dialog">&times;</button>
        </div>

        <form id="approveForm"
              method="POST"
              data-prevent-double-submit
              data-submitting-text="Approving Request...">
            @csrf
            @if(!empty($handymen) && $handymen->isNotEmpty())
                <div style="margin-bottom: 16px;">
                    <label class="admin-ticket-modal-label" for="approve_assigned_to">
                        Assign staff (optional)
                    </label>
                    <select id="approve_assigned_to"
                            name="assigned_to"
                            style="width:100%; padding:10px 14px; border-radius:12px; background:rgba(37,39,42,0.9); border:1px solid #3A342D; color:#F8F3EA; font-family:inherit; font-size:0.9rem;">
                        <option value="">Leave unassigned for now</option>
                        @foreach($handymen as $handyman)
                            <option value="{{ $handyman->id }}">{{ $handyman->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="admin-ticket-modal-actions">
                <button type="button" class="admin-ticket-modal-secondary" onclick="closeApproveModal()">Cancel</button>
                <button type="submit" class="admin-ticket-modal-primary">Approve Request</button>
            </div>
        </form>
    </div>
</div>

{{-- REJECT MODAL --}}
<div id="rejectModal"
     class="admin-ticket-modal-backdrop fixed inset-0 bg-black/70 hidden items-center justify-center z-50 backdrop-blur-sm"
     style="display: none;">
    <div class="admin-ticket-modal">
        <div class="admin-ticket-modal-head">
            <div>
                <h3>Reject Request</h3>
                <p>Provide a clear reason so the resident understands the decision.</p>
            </div>
            <button type="button" onclick="closeRejectModal()" aria-label="Close reject dialog">&times;</button>
        </div>

        <form id="rejectForm"
              method="POST"
              data-prevent-double-submit
              data-submitting-text="Rejecting Request...">
            @csrf
            <label class="admin-ticket-modal-label" for="queue_rejection_reason">Reason for rejection</label>
            <textarea id="queue_rejection_reason"
                      name="rejection_reason"
                      class="admin-ticket-modal-textarea"
                      rows="4"
                      maxlength="500"
                      required></textarea>

            <div class="admin-ticket-modal-actions">
                <button type="button" class="admin-ticket-modal-secondary" onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="admin-ticket-modal-danger">Reject Request</button>
            </div>
        </form>
    </div>
</div>
