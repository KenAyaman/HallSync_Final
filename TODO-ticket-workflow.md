# TODO: Complete Ticket Workflow Updates

**Information Gathered:**
- Current TicketController.php: Has index, create, store (status='received'), show, edit, update, destroy, updateStatus, assign.
- routes/web.php: Has resource tickets, update-status, assign (no approve/reject).
- View updated with cards; needs Approve/Reject buttons for 'pending_approval'.
- New migration exists for status enum.

**Plan:**
1. Replace entire app/Http/Controllers/TicketController.php with provided code (index role-based queries with FIELD ordering, store 'pending_approval', new approve/reject methods).
2. Add 3 new routes to routes/web.php under TICKET ROUTES.
3. Update resources/views/admin/tickets/index.blade.php: Add Approve/Reject buttons in ACTION BUTTONS div for pending_approval status, plus approve/reject modals/JS like assign modal.
4. Run migration if needed.

**Dependent Files:** None additional.

**Followup:** php artisan migrate (if new migration pending), test workflow: create ticket -> pending -> approve/reject/assign.

Confirm before implementing?
