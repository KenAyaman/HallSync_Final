# Typography Enhancement Plan - Manager/Admin Dashboard

## Status: 🚀 In Progress

- [ ] **Step 1**: Add global `body { font-size: 15px; }` to `resources/views/layouts/app.blade.php`


- [x] **Step 2**: Update stat cards in `resources/views/layouts/handyman.blade.php` ✅ (numbers updated, labels/descriptions inline)



- [x] **Step 3**: Hero h1/p updated in `resources/views/dashboard/handyman.blade.php` ✅


  - Hero: `text-4xl md:text-5xl lg:text-6xl font-bold` → `text-5xl md:text-6xl lg:text-7xl font-bold`
  - Hero p: `text-base md:text-lg` → `text-lg md:text-xl`
  - 4x stat spans: `text-3xl font-bold` → `text-4xl font-bold`
  - 4x labels: `text-sm font-semibold` → `text-base font-semibold`
  - 4x desc: `text-xs mt-1` → `text-sm mt-1`
  - Urgent h3: `text-lg font-bold` → `text-xl md:text-2xl font-bold`
  - Ticket title: `font-semibold text-white` → `text-base md:text-lg font-semibold text-white`
  - 2x ticket h3: `text-lg font-bold text-white` → `text-xl md:text-2xl font-bold text-white`
  - 8+ buttons: `px-4 py-2 rounded-xl text-xs font-semibold` → `px-5 py-2.5 rounded-xl text-sm font-semibold`
- [ ] **Step 4**: Update `resources/views/admin/tickets/index.blade.php`
  - Hero h1/p updates
  - Buttons `px-4 py-2 text-xs` → `px-5 py-2.5 text-sm`
  - Labels `text-xs font-semibold` → `text-sm font-semibold`
- [ ] **Step 5**: Hero updates in `resources/views/admin/bookings/index.blade.php` and `resources/views/dashboard.blade.php`
- [ ] **Step 6**: Run `npm run dev` to rebuild CSS
- [ ] **Step 7**: Test in browser (`php artisan serve`)
  - Check manager/handyman/admin dashboards
  - Verify responsive breakpoints (mobile/tablet/desktop)
  - Confirm no layout breaks
- [ ] **Step 8**: ✅ Complete - use `attempt_completion`

**Notes**:
- Skip `dashboard/manager.blade.php` (uses custom CSS classes, already sized well)
- All changes use exact string replacement for precision
- Changes enhance readability/impact without breaking layouts
