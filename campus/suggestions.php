<?php
$page_title = "View Suggestions";
include('templates/header.php');

// --- Filtering, Searching, and Sorting Logic ---
$category_filter = $_GET['category'] ?? '';
$status_filter = $_GET['status'] ?? '';
$sort_order = $_GET['sort'] ?? 'votes';
$search_term = $_GET['search'] ?? '';

// Use prepared statements for security
$params = [];
$types = '';

// Base Query
$sql = "SELECT * FROM suggestions WHERE 1=1";

// Append filters
if (!empty($search_term)) {
    $sql .= " AND (suggestion LIKE ? OR name LIKE ?)";
    $search_like = "%{$search_term}%";
    $params[] = $search_like;
    $params[] = $search_like;
    $types .= 'ss';
}
if (!empty($category_filter)) {
    $sql .= " AND category = ?";
    $params[] = $category_filter;
    $types .= 's';
}
if (!empty($status_filter)) {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

// Append sorting
switch ($sort_order) {
    case 'newest':
        $sql .= " ORDER BY created_at DESC";
        break;
    case 'oldest':
        $sql .= " ORDER BY created_at ASC";
        break;
    default: // 'votes'
        $sql .= " ORDER BY (upvotes - downvotes) DESC, created_at DESC";
        break;
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$categories_result = $conn->query("SELECT DISTINCT category FROM suggestions ORDER BY category ASC");
?>

<h2 class="text-center mb-4">All Suggestions</h2>

<div class="card suggestion-card mb-4">
    <div class="card-body">
        <form method="GET" action="suggestions.php" class="row g-3 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search suggestions..." value="<?php echo htmlspecialchars($search_term); ?>">
            </div>
            <div class="col-md-2">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php while ($cat = $categories_result->fetch_assoc()): ?>
                        <option value="<?php echo $cat['category']; ?>" <?php if ($category_filter == $cat['category']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($cat['category']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-2">
                 <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="Pending" <?php if ($status_filter == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Approved" <?php if ($status_filter == 'Approved') echo 'selected'; ?>>Approved</option>
                    <option value="Implemented" <?php if ($status_filter == 'Implemented') echo 'selected'; ?>>Implemented</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="sort" class="form-select">
                    <option value="votes" <?php if ($sort_order == 'votes') echo 'selected'; ?>>Most Voted</option>
                    <option value="newest" <?php if ($sort_order == 'newest') echo 'selected'; ?>>Newest First</option>
                    <option value="oldest" <?php if ($sort_order == 'oldest') echo 'selected'; ?>>Oldest First</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()):
            $total_votes = $row['upvotes'] + $row['downvotes'];
            $up_percentage = $total_votes > 0 ? ($row['upvotes'] / $total_votes) * 100 : 0;
            $down_percentage = $total_votes > 0 ? ($row['downvotes'] / $total_votes) * 100 : 0;
            $status_class = 'status-' . strtolower($row['status']);
        ?>
            <div class="col">
                <div class="card h-100 suggestion-card shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($row['category']); ?></h5>
                            <span class="badge rounded-pill <?php echo $status_class; ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                        </div>
                        <p class="card-text flex-grow-1"><?php echo nl2br(htmlspecialchars($row['suggestion'])); ?></p>
                        <small class="text-muted">By: <?php echo htmlspecialchars($row['name']); ?> on <?php echo date('d M Y', strtotime($row['created_at'])); ?></small>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <div class="vote-bar-container mb-2">
                            <div class="vote-bar-upvotes" data-id="<?php echo $row['id']; ?>" style="width: <?php echo $up_percentage; ?>%;"></div>
                            <div class="vote-bar-downvotes" data-id="<?php echo $row['id']; ?>" style="width: <?php echo $down_percentage; ?>%;"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-success vote-btn" data-id="<?php echo $row['id']; ?>" data-type="upvote">
                                    <i class="fas fa-thumbs-up"></i> <span id="upvotes-count-<?php echo $row['id']; ?>"><?php echo $row['upvotes']; ?></span>
                                </button>
                                <button class="btn btn-sm btn-outline-danger vote-btn" data-id="<?php echo $row['id']; ?>" data-type="downvote">
                                    <i class="fas fa-thumbs-down"></i> <span id="downvotes-count-<?php echo $row['id']; ?>"><?php echo $row['downvotes']; ?></span>
                                </button>
                            </div>
                            <a href="#" class="text-decoration-none text-primary"><i class="fas fa-comment"></i> Comments</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <p class="text-center text-muted fs-4 mt-5">No suggestions found matching your criteria.</p>
        </div>
    <?php endif; $stmt->close(); ?>
</div>

<?php include('templates/footer.php'); ?>