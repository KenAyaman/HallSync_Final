# HallSync Concern Management System

## Architecture

The concern module is a private case-management workflow, not a generic CRUD screen. `Concern` is the case record. Separate tables retain communication, evidence, assignments, status history, internal notes, and audits so investigation records remain traceable.

Resident flow:

```text
Draft -> Submitted -> Under Review -> Investigation Ongoing
      -> Awaiting Resident Response -> Resolved -> Closed
Submitted -> Rejected
Resolved -> Reopened -> Under Review
```

Only transitions declared in `Concern::TRANSITIONS` are accepted. Each transition records the actor, reason, and timestamp.

## Database Design

| Table | Purpose | Important indexes |
| --- | --- | --- |
| `concerns` | Case identity, category, automatic priority, lifecycle timestamps, resolution | `priority + status`, `category + created_at`, `handled_by + status` |
| `concern_messages` | Private resident-administration thread | `concern_id + created_at` |
| `concern_evidence` | Private file metadata, hash, uploader, size, MIME type | `concern_id + created_at` |
| `concern_assignments` | Assignment and reassignment history | `concern_id + ended_at`, `assigned_to + ended_at` |
| `concern_status_histories` | Durable lifecycle timeline | `concern_id + created_at` |
| `concern_internal_notes` | Staff-only investigation notes | `concern_id + created_at` |
| `concern_audit_logs` | Access and action trail with actor, IP, and user agent | `concern_id + created_at`, `actor_id + created_at` |

## Security Design

- Residents can only open their own cases.
- Managers can manage cases and staff-only notes.
- Evidence uses the private `local` disk and an authorized download route. It is not exposed through `/storage`.
- Uploads are limited to JPG, PNG, WEBP, MP4, MOV, and PDF with a 10MB per-file limit and SHA-256 hashes.
- Resident edits and deletion stop once review begins to preserve investigation integrity.
- Reopening requires a reason and is limited to two attempts.
- Anonymous reporting hides identity from involved parties while retaining administrator visibility.
- `ConcernPolicy` documents ownership and manager privileges; controller checks enforce each endpoint.

## Analytics Design

The manager dashboard presents pending cases, investigations, cases awaiting resident response, monthly resolutions, overdue work, urgent volume, high-priority workload, the most common category, average resolution time, and resolution rate. Each card includes an operational interpretation so analytics support management decisions rather than act as decoration.

## Edge And Abuse Cases

- Invalid lifecycle jumps are rejected with validation errors.
- A resident cannot access another resident's case or evidence.
- A forged evidence ID is rejected unless it belongs to the requested concern.
- Unsupported files and oversized files are rejected.
- Resolved cases cannot be reopened indefinitely.
- Closed and rejected concerns remain in history for audit and trend analysis.

## Thesis Defense Notes

**Why use separate history tables instead of one status field?**  
The current status supports fast filtering; history tables preserve accountability. Investigators can explain who changed a case, when it changed, and why.

**Why keep evidence private?**  
Concern evidence can include sensitive resident information. A public storage URL bypasses ownership checks. Authorized downloads protect confidentiality and create an access trail.

**Why auto-calculate priority?**  
Residents may underestimate or exaggerate urgency. Category-based defaults produce consistent triage while allowing administrators to manage the queue predictably.

**Why limit edits after review begins?**  
Investigation decisions must be based on a stable report. Residents can still add messages and evidence without rewriting the original statement.

**How does the system support operations?**  
Assignments, due dates, overdue counts, lifecycle timestamps, internal notes, and management insights turn reports into manageable cases with clear accountability.
