# HallSync Manuscript System Alignment Notes

Checked file: `HallSync_revised_Finale.docx`

## Overall finding

The manuscript is connected to the HallSync system, but several parts still describe an earlier proposal/prototype version. The current Laravel system already includes panel-requested revisions that are not fully reflected in the manuscript.

## Parts to revise

1. Proposed Solutions, Digital Maintenance Ticketing System
   - Current manuscript says tickets move through `Received, Assigned, In Progress, and Completed`.
   - Actual system uses a fuller workflow: resident submission, admin approval or rejection with reason, staff assignment, in-progress work, resolved/completed repair, then resident closure or reopening.
   - Add task start time, completion time, and duration tracking.

2. Proposed Solutions, Facility Booking Calendar
   - Current manuscript says the system only shows free slots and prevents conflicts by making slots unavailable.
   - Actual system uses capacity-based booking. Study Room 1 and Study Room 2 are 25 slots each. The interface shows slot occupancy such as `Capacity: 0/25`, then updates to `1/25` when a resident books.
   - Bookings are automatically confirmed/reserved for residents; they do not require admin approval. A slot only becomes full when reserved count reaches facility capacity.
   - The system also prevents one resident from booking overlapping schedules across facilities.

3. Proposed Solutions, Broadcast Announcement System
   - Current manuscript mentions optional email copies.
   - Actual system publishes in-app announcements with priority, active/inactive visibility, start date, expiration, and pinned announcements. Real-time dashboard updates are handled through Laravel Echo/Reverb with a heartbeat fallback.

4. Proposed Solutions, Community Hub
   - Current manuscript says noise complaints are submitted privately through the Community Hub.
   - Actual system separates public community posts from private concerns/complaints. Community supports Discussion, Lost & Found, Buy & Sell, Event, and Other categories with admin moderation. Private concerns have their own workflow, evidence, replies, assignment, status history, and audit logs.

5. Proposed Solutions / Chapter 1
   - The manuscript should mention the implemented admin dashboard analytics because the current system includes descriptive, diagnostic, predictive, and prescriptive analytics, not just charts.

6. Proposed Solutions / Chapter 1
   - Add audit logging. The current system records operational activity for booking, ticket, announcement, concern, community moderation, user management, login, and password actions.

7. Prototype and Test sections
   - These sections still say the project is a low-fidelity prototype and that feedback will guide a working prototype before development begins.
   - For a final/revised manuscript, change this to say the low-fidelity prototype guided development of the working Laravel system.

8. Minor text issue
   - `resident-related acztivities` should be corrected to `resident-related activities`.

