# Remaining Work for 2026/2027 File System

## ✅ Completed
- Added `file_name` column to trips table with auto-migration
- Updated API `addTrip` to accept `file_name` parameter
- Started removal of guest info system

## 🔄 Part B: Remove Guest Info System (IN PROGRESS)

### 1. Remove HTML from Itinerary.php
- **Lines 1007-1290**: Delete entire `<div id="guestInfoView">...</div>` section
- **Line 2640**: Remove guest info icon button from day headers:
  ```html
  <button type="button" onclick="showGuestInfo()" ...>
  ```

### 2. Remove JavaScript Functions
Delete these functions from Itinerary.php (around lines 1690-2340):
- `populateGuestInfo()`
- `showGuestInfo()` 
- `showGuestStep()`
- `renderGuestList()`
- `collectGuestDetails()`
- `collectArrivalDetails()`
- `saveGuestInfo()`
- `saveGuestStatusThenShowStep2()`
- `renderUnassignedGuests()`
- `renderUnassignedGuestsDep()`
- `renderArrivalGroups()`
- `renderDepartureGroups()`
- All drag-and-drop handlers (handleDragStart, handleDrop, etc.)
- All arrival/departure group state variables

### 3. Remove Guest Info Buttons/Listeners
- Remove `guestInfoSaveBtn` listener
- Remove `guestInfoContinueBtn` listener
- Remove `giNextBtn` listener
- Remove `giBackBtn` listener
- Remove `gi_add_guest_btn` listener
- Remove all arrival/departure group creation buttons

## 📝 Part C: Update UI to use file_name

### 1. Update index.php - Trip Creation Modal
Find and update the trip form (around line 2600-2900):
- Change "Customer Name" label to "File Name"
- Change input field from `customer_name` to `file_name`:
  ```html
  <label for="file_name">File Name</label>
  <input type="text" id="file_name" name="file_name" required>
  ```

### 2. Update index.php - Trip Display
- Find `renderTrips()` function
- Change `trip.customer_name` to `trip.file_name`
- Remove `trip.id` display from trip cards/listings
- Update trip headers to show file_name

### 3. Update Itinerary.php Header
Around line 3120-3150:
- Change `${trip.customer_name}'s ${trip.package_name}` to `${trip.file_name}`
- Remove file ID display if present

### 4. Update API getTrips
In api.php:
- Ensure SELECT queries include `file_name`
- Update any customer_name references

## 🆕 Part D: New Features

### 1. Add Package Records View (Insights)
Create new view in index.php insights section:

```javascript
async function fetchPackageRecords() {
  const response = await fetch(`${API_URL}?action=getPackageRecords`);
  const result = await response.json();
  if (result.status === 'success') {
    renderPackageRecords(result.data);
  }
}

function renderPackageRecords(data) {
  const container = document.getElementById('packageRecordsContainer');
  // Group trips by package
  const byPackage = {};
  data.forEach(trip => {
    if (!byPackage[trip.package_name]) {
      byPackage[trip.package_name] = [];
    }
    byPackage[trip.package_name].push(trip);
  });
  
  let html = '';
  Object.keys(byPackage).sort().forEach(pkgName => {
    const trips = byPackage[pkgName];
    html += `
      <div class="package-record-group">
        <h3>${pkgName} (${trips.length} files)</h3>
        <div class="package-trips">
          ${trips.map(t => `
            <div class="trip-record">
              <span class="file-name">${t.file_name}</span>
              <span class="dates">${t.start_date} to ${t.end_date}</span>
              <a href="Itinerary.php?trip_id=${t.id}">View</a>
            </div>
          `).join('')}
        </div>
      </div>
    `;
  });
  container.innerHTML = html;
}
```

Add API endpoint in api.php:
```php
function getPackageRecords($conn) {
    $sql = "SELECT t.id, t.file_name, t.start_date, t.end_date, 
                   p.name as package_name
            FROM trips t
            JOIN trip_packages p ON t.trip_package_id = p.id
            ORDER BY p.name, t.start_date";
    $result = $conn->query($sql);
    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $records]);
}
```

### 2. Force Password Change for New Users

#### Database Migration (api.php):
```php
// In database schema helper
$res = $conn->query("SHOW COLUMNS FROM users LIKE 'must_change_password'");
if (!$res || $res->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN must_change_password TINYINT(1) DEFAULT 0");
}
```

#### Update login.php:
```php
// After successful login check
if ($user['must_change_password'] == 1) {
    $_SESSION['force_password_change'] = true;
    header('Location: change_password.php');
    exit();
}
```

#### Create change_password.php:
```php
<?php
session_start();
if (!isset($_SESSION['force_password_change'])) {
    header('Location: index.php');
    exit();
}
// Form to change password
// On success: UPDATE users SET must_change_password = 0
// unset($_SESSION['force_password_change'])
?>
```

#### Update user creation:
When admin creates new user, set `must_change_password = 1`

## 🔧 Quick Commands

```bash
# To delete lines in Itinerary.php (use your editor)
# Lines to delete: 1007-1290 (guest info HTML)
# Lines to delete: ~1690-2340 (guest info JS functions)

# Test the changes
git add .
git commit -m "Remove guest info system and update to file_name"
git push origin main
```

## Testing Checklist
- [ ] Can create new trip with file_name
- [ ] Trip listings show file_name instead of customer_name
- [ ] No guest info button in itinerary
- [ ] No guest info view appears
- [ ] Itinerary works without guest info
- [ ] Package Records view shows trips grouped by package
- [ ] New users are forced to change password on first login
