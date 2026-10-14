<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';
require 'includes/date_helpers.php';
require 'includes/validasi.php';

$allowedStatuses = ['belum', 'progres', 'selesai'];
$allowedFilters = ['semua', ...$allowedStatuses];
$filterInput = $_GET['filter'] ?? 'semua';
$filter = is_string($filterInput) && in_array($filterInput, $allowedFilters, true) ? $filterInput : 'semua';
$searchInput = $_GET['cari'] ?? '';
$searchTerm = is_string($searchInput) ? trim($searchInput) : '';

$taskReturnUrl = static function (string $returnFilter, string $returnSearch): string {
    $query = ['filter' => in_array($returnFilter, ['semua', 'belum', 'progres', 'selesai'], true) ? $returnFilter : 'semua'];
    if ($returnSearch !== '') {
        $query['cari'] = $returnSearch;
    }

    return 'tugas.php?' . http_build_query($query);
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $returnFilterInput = $_POST['return_filter'] ?? 'semua';
    $returnSearchInput = $_POST['return_search'] ?? '';
    $returnFilter = is_string($returnFilterInput) ? $returnFilterInput : 'semua';
    $returnSearch = is_string($returnSearchInput) ? trim($returnSearchInput) : '';
    $redirectUrl = $taskReturnUrl($returnFilter, $returnSearch);
    $actionInput = $_POST['action'] ?? '';
    $action = is_string($actionInput) ? $actionInput : '';

    try {
        if ($action === 'create' || $action === 'update') {
            $titleInput = $_POST['title'] ?? '';
            $deadlineInput = $_POST['deadline'] ?? '';
            $statusInput = $_POST['status'] ?? 'belum';
            $title = is_string($titleInput) ? trim($titleInput) : '';
            $deadline = is_string($deadlineInput) ? $deadlineInput : '';
            $status = is_string($statusInput) ? $statusInput : '';
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

            $error = validasiNamaTugas($title);
            $deadlineDate = DateTimeImmutable::createFromFormat('!Y-m-d', $deadline);
            $dateErrors = DateTimeImmutable::getLastErrors();
            if ($error === null && (!$deadlineDate || $deadlineDate->format('Y-m-d') !== $deadline || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0)))) {
                $error = 'Format deadline tidak valid.';
            }
            if ($error === null && !in_array($status, $allowedStatuses, true)) {
                $error = 'Status tugas tidak valid.';
            }

            $taskDate = null;
            if ($action === 'create' && $error === null) {
                $todayResult = mysqli_query($koneksi, 'SELECT CURRENT_DATE AS today');
                $taskDate = mysqli_fetch_assoc($todayResult)['today'];
            } elseif ($action === 'update') {
                if ($id === false) {
                    $error = 'ID tugas tidak valid.';
                } else {
                    $stmt = mysqli_prepare($koneksi, 'SELECT task_date FROM tasks WHERE id = ? AND user_id = ?');
                    mysqli_stmt_bind_param($stmt, 'ii', $id, $_SESSION['user_id']);
                    mysqli_stmt_execute($stmt);
                    $ownedTask = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                    if (!$ownedTask) {
                        $error = 'Tugas tidak ditemukan atau tidak dapat diubah.';
                    } else {
                        $taskDate = $ownedTask['task_date'];
                    }
                }
            }

            if ($error === null && $deadline < $taskDate) {
                $error = 'Deadline tidak boleh sebelum tanggal dibuat.';
            }

            if ($error !== null) {
                $_SESSION['task_notice'] = ['type' => 'error', 'message' => $error];
                $_SESSION['task_form_state'] = [
                    'mode' => $action,
                    'id' => $id === false ? null : $id,
                    'title' => $title,
                    'deadline' => $deadline,
                    'status' => $status,
                ];
            } elseif ($action === 'create') {
                $stmt = mysqli_prepare($koneksi, 'INSERT INTO tasks (user_id, title, task_date, deadline, status) VALUES (?, ?, CURRENT_DATE, ?, ?)');
                mysqli_stmt_bind_param($stmt, 'isss', $_SESSION['user_id'], $title, $deadline, $status);
                mysqli_stmt_execute($stmt);
                $_SESSION['task_notice'] = ['type' => 'success', 'message' => 'Tugas berhasil ditambahkan.'];
            } else {
                $stmt = mysqli_prepare($koneksi, 'UPDATE tasks SET title = ?, deadline = ?, status = ? WHERE id = ? AND user_id = ?');
                mysqli_stmt_bind_param($stmt, 'sssii', $title, $deadline, $status, $id, $_SESSION['user_id']);
                mysqli_stmt_execute($stmt);
                $_SESSION['task_notice'] = ['type' => 'success', 'message' => 'Perubahan tugas berhasil disimpan.'];
            }
        } elseif ($action === 'delete') {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($id === false) {
                $_SESSION['task_notice'] = ['type' => 'error', 'message' => 'ID tugas tidak valid.'];
            } else {
                $stmt = mysqli_prepare($koneksi, 'DELETE FROM tasks WHERE id = ? AND user_id = ?');
                mysqli_stmt_bind_param($stmt, 'ii', $id, $_SESSION['user_id']);
                mysqli_stmt_execute($stmt);
                if (mysqli_stmt_affected_rows($stmt) !== 1) {
                    $_SESSION['task_notice'] = ['type' => 'error', 'message' => 'Tugas tidak ditemukan atau tidak dapat dihapus.'];
                } else {
                    $_SESSION['task_notice'] = ['type' => 'success', 'message' => 'Tugas berhasil dihapus.'];
                }
            }
        } else {
            $_SESSION['task_notice'] = ['type' => 'error', 'message' => 'Operasi tugas tidak valid.'];
        }
    } catch (mysqli_sql_exception $exception) {
        error_log('Task operation failed: ' . $exception->getMessage());
        $_SESSION['task_notice'] = ['type' => 'error', 'message' => 'Tugas tidak dapat disimpan saat ini. Silakan coba lagi.'];
        if (($action === 'create' || $action === 'update') && isset($title, $deadline, $status)) {
            $_SESSION['task_form_state'] = [
                'mode' => $action,
                'id' => $id === false ? null : $id,
                'title' => $title,
                'deadline' => $deadline,
                'status' => $status,
            ];
        }
    }

    header('Location: ' . $redirectUrl);
    exit;
}

$notice = $_SESSION['task_notice'] ?? null;
unset($_SESSION['task_notice']);
$formState = $_SESSION['task_form_state'] ?? null;
unset($_SESSION['task_form_state']);
$createInput = $_GET['tambah'] ?? '';
$createOpen = (is_array($formState) && $formState['mode'] === 'create') || $createInput === '1';
$editingTaskId = is_array($formState) && $formState['mode'] === 'update' ? (int) $formState['id'] : 0;

$sql = 'SELECT id, title, task_date, deadline, status FROM tasks WHERE user_id = ?';
if ($filter !== 'semua') {
    $sql .= ' AND status = ?';
}
if ($searchTerm !== '') {
    $sql .= ' AND title LIKE ?';
}
$sql .= " ORDER BY CASE WHEN status = 'selesai' THEN 1 ELSE 0 END ASC, deadline ASC, task_date ASC, id ASC";

$stmt = mysqli_prepare($koneksi, $sql);
if ($filter !== 'semua' && $searchTerm !== '') {
    $keyword = '%' . $searchTerm . '%';
    mysqli_stmt_bind_param($stmt, 'iss', $_SESSION['user_id'], $filter, $keyword);
} elseif ($filter !== 'semua') {
    mysqli_stmt_bind_param($stmt, 'is', $_SESSION['user_id'], $filter);
} elseif ($searchTerm !== '') {
    $keyword = '%' . $searchTerm . '%';
    mysqli_stmt_bind_param($stmt, 'is', $_SESSION['user_id'], $keyword);
} else {
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
}
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

$statusLabels = ['belum' => 'Belum', 'progres' => 'Progres', 'selesai' => 'Selesai'];
$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<?php include 'includes/header.php'; ?>

<section class="tasks-page">
    <header class="tasks-page-header">
        <div>
            <h1>Tugas</h1>
            <p>Kelola dan pantau tugasmu dengan lebih mudah.</p>
        </div>
        <button class="add-task-button" type="button" id="show-create-task">+ Tambah Tugas</button>
    </header>

    <?php if ($notice): ?>
        <p class="task-notice <?= $escape($notice['type']) ?>" role="<?= $notice['type'] === 'error' ? 'alert' : 'status' ?>"><?= $escape($notice['message']) ?></p>
    <?php endif; ?>

    <form class="tasks-toolbar" action="tugas.php" method="GET" role="search">
        <div class="task-search-field">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.2 4.2"/></svg>
            <input type="search" name="cari" value="<?= $escape($searchTerm) ?>" placeholder="Cari tugas..." aria-label="Cari tugas">
            <button class="task-search-submit" type="submit" aria-label="Cari tugas">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.2 4.2"/></svg>
            </button>
        </div>
        <label class="task-filter-field">
            <span class="visually-hidden">Filter status</span>
            <select name="filter">
                <?php foreach (['semua' => 'Semua Status', 'belum' => 'Belum', 'progres' => 'Progres', 'selesai' => 'Selesai'] as $value => $label): ?>
                    <option value="<?= $escape($value) ?>" <?= $filter === $value ? 'selected' : '' ?>><?= $escape($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </form>

    <div class="task-table-scroll">
        <table class="task-table">
            <thead>
                <tr>
                    <th scope="col">Nama Tugas</th>
                    <th scope="col">Dibuat</th>
                    <th scope="col">Deadline</th>
                    <th scope="col">Status</th>
                    <th scope="col" aria-label="Aksi"></th>
                </tr>
            </thead>
            <tbody>
                    <tr class="task-create-row" <?= $createOpen ? '' : 'hidden' ?>>
                        <td>
                            <form id="task-create-form" action="tugas.php" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="create">
                                <input type="hidden" name="return_filter" value="<?= $escape($filter) ?>">
                                <input type="hidden" name="return_search" value="<?= $escape($searchTerm) ?>">
                            </form>
                            <input class="task-table-input" form="task-create-form" type="text" name="title" maxlength="150" required placeholder="Nama tugas" value="<?= $escape($formState['title'] ?? '') ?>" aria-label="Nama tugas">
                        </td>
                        <td class="task-date-auto">Otomatis</td>
                        <td><input class="task-table-input task-date-input" form="task-create-form" type="date" name="deadline" required value="<?= $escape($formState['deadline'] ?? '') ?>" aria-label="Deadline"></td>
                        <td>
                            <select class="task-status-select" form="task-create-form" name="status" aria-label="Status tugas">
                                <?php foreach ($statusLabels as $value => $label): ?>
                                    <option value="<?= $escape($value) ?>" <?= ($formState['status'] ?? 'belum') === $value ? 'selected' : '' ?>><?= $escape($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="task-row-actions">
                            <button class="task-save-button" type="submit" form="task-create-form">Simpan</button>
                            <button class="task-cancel-button" type="button" data-cancel-create>Batal</button>
                        </td>
                    </tr>

                <?php $hasTasks = false; ?>
                <?php while ($row = mysqli_fetch_assoc($hasil)): $hasTasks = true; ?>
                    <?php
                    $isEditing = $editingTaskId === (int) $row['id'];
                    $editTitle = $isEditing ? ($formState['title'] ?? $row['title']) : $row['title'];
                    $editDeadline = $isEditing ? ($formState['deadline'] ?? $row['deadline']) : $row['deadline'];
                    $editStatus = $isEditing ? ($formState['status'] ?? $row['status']) : $row['status'];
                    $editFormId = 'task-edit-form-' . (int) $row['id'];
                    ?>
                    <tr class="task-table-row<?= $isEditing ? ' is-editing' : '' ?>" data-task-row data-server-edit="<?= $isEditing ? 'true' : 'false' ?>" data-task-id="<?= $escape($row['id']) ?>" data-task-title="<?= $escape($row['title']) ?>" data-task-date="<?= $escape($row['task_date']) ?>" data-task-deadline="<?= $escape($row['deadline']) ?>" data-task-status="<?= $escape($row['status']) ?>">
                        <td>
                            <?php if ($isEditing): ?>
                                <form id="<?= $escape($editFormId) ?>" action="tugas.php" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?= $escape($row['id']) ?>">
                                    <input type="hidden" name="return_filter" value="<?= $escape($filter) ?>">
                                    <input type="hidden" name="return_search" value="<?= $escape($searchTerm) ?>">
                                </form>
                                <input class="task-table-input" form="<?= $escape($editFormId) ?>" type="text" name="title" maxlength="150" required value="<?= $escape($editTitle) ?>" aria-label="Nama tugas">
                            <?php else: ?>
                                <strong><?= $escape($row['title']) ?></strong>
                            <?php endif; ?>
                        </td>
                        <td><time datetime="<?= $escape($row['task_date']) ?>"><?= $escape(formatTanggalIndonesia($row['task_date'])) ?></time></td>
                        <td>
                            <?php if ($isEditing): ?>
                                <input class="task-table-input task-date-input" form="<?= $escape($editFormId) ?>" type="date" name="deadline" required value="<?= $escape($editDeadline) ?>" aria-label="Deadline">
                            <?php else: ?>
                                <time datetime="<?= $escape($row['deadline']) ?>"><?= $escape(formatTanggalIndonesia($row['deadline'])) ?></time>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($isEditing): ?>
                                <select class="task-status-select" form="<?= $escape($editFormId) ?>" name="status" aria-label="Status tugas">
                                    <?php foreach ($statusLabels as $value => $label): ?>
                                        <option value="<?= $escape($value) ?>" <?= $editStatus === $value ? 'selected' : '' ?>><?= $escape($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <form class="task-inline-status" action="task_status.php" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $escape($row['id']) ?>">
                                    <label>
                                        <span class="visually-hidden">Status untuk <?= $escape($row['title']) ?></span>
                                        <select class="task-status-select status-<?= $escape($row['status']) ?>" name="status" data-save-status data-current-status="<?= $escape($row['status']) ?>">
                                            <?php foreach ($statusLabels as $value => $label): ?>
                                                <option value="<?= $escape($value) ?>" <?= $row['status'] === $value ? 'selected' : '' ?>><?= $escape($label) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                    <span class="task-status-saving" hidden role="status">Menyimpan…</span>
                                </form>
                            <?php endif; ?>
                        </td>
                        <td class="task-row-actions">
                            <?php if ($isEditing): ?>
                                <span class="task-edit-actions">
                                    <button class="task-save-button" type="submit" form="<?= $escape($editFormId) ?>">Simpan Perubahan</button>
                                    <button class="task-cancel-button" type="button" data-cancel-edit>Batal</button>
                                </span>
                            <?php else: ?>
                                <details class="task-actions">
                                    <summary aria-label="Aksi untuk <?= $escape($row['title']) ?>">⋮</summary>
                                    <div class="task-action-menu">
                                        <button type="button" data-edit-task>Edit</button>
                                        <button type="button" class="delete-task-trigger" data-task-id="<?= $escape($row['id']) ?>" data-task-title="<?= $escape($row['title']) ?>">Hapus</button>
                                    </div>
                                </details>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>

                <?php if (!$createOpen && !$hasTasks): ?>
                    <tr><td class="task-table-empty" colspan="5"><?= $searchTerm !== '' ? 'Tidak ada tugas yang cocok dengan pencarian.' : 'Belum ada tugas untuk filter ini.' ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<div class="task-delete-modal" id="task-delete-modal" hidden>
    <section class="task-delete-dialog" role="dialog" aria-modal="true" aria-labelledby="task-delete-heading" aria-describedby="task-delete-message" tabindex="-1">
        <span class="task-delete-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M4 7h16M10 11v6m4-6v6M5.5 7l1 13h11l1-13M9 7V4h6v3"/></svg>
        </span>
        <h2 id="task-delete-heading">Hapus tugas?</h2>
        <p id="task-delete-message">Penghapusan tidak dapat dibatalkan. Tugas berikut akan dihapus:</p>
        <p class="task-delete-title" id="task-delete-title"></p>
        <form id="task-delete-form" action="tugas.php" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="task-delete-id">
            <input type="hidden" name="return_filter" value="<?= $escape($filter) ?>">
            <input type="hidden" name="return_search" value="<?= $escape($searchTerm) ?>">
            <div class="task-delete-actions">
                <button class="task-delete-cancel" type="button" data-close-task-modal>Batal</button>
                <button class="task-delete-confirm" type="submit">Hapus</button>
            </div>
        </form>
    </section>
</div>

<script>
(() => {
    const createRow = document.querySelector('.task-create-row');
    const addButton = document.getElementById('show-create-task');
    const modal = document.getElementById('task-delete-modal');
    const deleteTitle = document.getElementById('task-delete-title');
    const deleteId = document.getElementById('task-delete-id');
    const cancelModal = modal.querySelector('[data-close-task-modal]');
    let activeDeleteTrigger = null;
    let activeEdit = null;

    const closeTaskModal = () => {
        modal.hidden = true;
        document.body.classList.remove('task-delete-modal-open');
        activeDeleteTrigger?.focus();
        activeDeleteTrigger = null;
    };

    const showTaskNotice = (message, type) => {
        let notice = document.querySelector('.task-notice');
        if (!notice) {
            notice = document.createElement('p');
            notice.className = 'task-notice';
            document.querySelector('.tasks-page-header').after(notice);
        }
        notice.className = `task-notice ${type}`;
        notice.setAttribute('role', type === 'error' ? 'alert' : 'status');
        notice.textContent = message;
    };

    const restoreActiveEdit = () => {
        if (activeEdit) {
            activeEdit.editRow.replaceWith(activeEdit.originalRow);
            activeEdit.originalRow.querySelector('[data-edit-task]')?.focus();
            activeEdit = null;
        }
    };

    const addHiddenInput = (form, name, value) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.append(input);
    };

    const startEdit = (originalRow) => {
        const serverEditingRow = document.querySelector('[data-server-edit="true"]');
        if (serverEditingRow) {
            const url = new URL(window.location.href);
            url.searchParams.set('edit', originalRow.dataset.taskId);
            url.searchParams.delete('tambah');
            window.location.assign(url);
            return;
        }

        restoreActiveEdit();
        createRow.hidden = true;
        originalRow.querySelector('details')?.removeAttribute('open');

        const id = originalRow.dataset.taskId;
        const formId = `task-edit-form-${id}`;
        const editRow = document.createElement('tr');
        editRow.className = 'task-table-row is-editing';
        editRow.dataset.taskRow = '';
        editRow.dataset.clientEdit = 'true';

        const titleCell = document.createElement('td');
        const form = document.createElement('form');
        form.id = formId;
        form.action = 'tugas.php';
        form.method = 'POST';
        addHiddenInput(form, 'csrf_token', document.querySelector('#task-create-form [name="csrf_token"]').value);
        addHiddenInput(form, 'action', 'update');
        addHiddenInput(form, 'id', id);
        const currentUrl = new URL(window.location.href);
        addHiddenInput(form, 'return_filter', currentUrl.searchParams.get('filter') || 'semua');
        addHiddenInput(form, 'return_search', currentUrl.searchParams.get('cari') || '');
        titleCell.append(form);

        const titleInput = document.createElement('input');
        titleInput.className = 'task-table-input';
        titleInput.type = 'text';
        titleInput.name = 'title';
        titleInput.maxLength = 150;
        titleInput.required = true;
        titleInput.value = originalRow.dataset.taskTitle;
        titleInput.setAttribute('aria-label', 'Nama tugas');
        titleInput.setAttribute('form', formId);
        titleCell.append(titleInput);

        const createdCell = originalRow.cells[1].cloneNode(true);
        const deadlineCell = document.createElement('td');
        const deadlineInput = document.createElement('input');
        deadlineInput.className = 'task-table-input task-date-input';
        deadlineInput.type = 'date';
        deadlineInput.name = 'deadline';
        deadlineInput.required = true;
        deadlineInput.value = originalRow.dataset.taskDeadline;
        deadlineInput.setAttribute('aria-label', 'Deadline');
        deadlineInput.setAttribute('form', formId);
        deadlineCell.append(deadlineInput);

        const statusCell = document.createElement('td');
        const statusSelect = document.createElement('select');
        statusSelect.className = 'task-status-select';
        statusSelect.name = 'status';
        statusSelect.setAttribute('aria-label', 'Status tugas');
        statusSelect.setAttribute('form', formId);
        [['belum', 'Belum'], ['progres', 'Progres'], ['selesai', 'Selesai']].forEach(([value, label]) => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = label;
            option.selected = value === originalRow.dataset.taskStatus;
            statusSelect.append(option);
        });
        statusCell.append(statusSelect);

        const actionsCell = document.createElement('td');
        actionsCell.className = 'task-row-actions';
        const actions = document.createElement('span');
        actions.className = 'task-edit-actions';
        const saveButton = document.createElement('button');
        saveButton.className = 'task-save-button';
        saveButton.type = 'submit';
        saveButton.setAttribute('form', formId);
        saveButton.textContent = 'Simpan Perubahan';
        const cancelButton = document.createElement('button');
        cancelButton.className = 'task-cancel-button';
        cancelButton.type = 'button';
        cancelButton.dataset.cancelEdit = '';
        cancelButton.textContent = 'Batal';
        actions.append(saveButton, cancelButton);
        actionsCell.append(actions);
        editRow.append(titleCell, createdCell, deadlineCell, statusCell, actionsCell);

        originalRow.replaceWith(editRow);
        activeEdit = { editRow, originalRow };
        titleInput.focus();
    };

    addButton.addEventListener('click', () => {
        if (document.querySelector('[data-server-edit="true"]')) {
            const url = new URL(window.location.href);
            url.searchParams.set('tambah', '1');
            url.searchParams.delete('edit');
            window.location.assign(url);
            return;
        }
        restoreActiveEdit();
        createRow.hidden = false;
        createRow.querySelector('input[name="title"]').focus();
    });

    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-cancel-create]')) {
            createRow.hidden = true;
        }

        if (event.target.closest('[data-edit-task]')) {
            const row = event.target.closest('[data-task-row]');
            startEdit(row);
        }

        if (event.target.closest('[data-cancel-edit]')) {
            if (activeEdit && activeEdit.editRow === event.target.closest('[data-task-row]')) {
                restoreActiveEdit();
            } else {
                window.location.reload();
            }
        }

        const deleteTrigger = event.target.closest('.delete-task-trigger');
        if (deleteTrigger) {
            activeDeleteTrigger = deleteTrigger;
            deleteId.value = deleteTrigger.dataset.taskId;
            deleteTitle.textContent = deleteTrigger.dataset.taskTitle;
            modal.hidden = false;
            document.body.classList.add('task-delete-modal-open');
            cancelModal.focus();
        } else if (event.target === modal || event.target.closest('[data-close-task-modal]')) {
            closeTaskModal();
        }
    });

    document.addEventListener('change', (event) => {
        if (event.target.matches('.task-filter-field select')) {
            event.target.form.requestSubmit();
        }
        if (event.target.matches('[data-save-status]')) {
            const select = event.target;
            const previousStatus = select.dataset.currentStatus;
            const row = select.closest('[data-task-row]');
            const savingNotice = select.form.querySelector('.task-status-saving');
            const formData = new FormData(select.form);
            select.disabled = true;
            row.setAttribute('aria-busy', 'true');
            savingNotice.hidden = false;

            fetch(select.form.action, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: { Accept: 'application/json' }
            }).then(async (response) => {
                let result;
                try {
                    result = await response.json();
                } catch {
                    throw new Error('Status tugas tidak dapat diperbarui. Silakan coba lagi.');
                }
                if (!response.ok || result.success !== true) {
                    throw new Error(result.message || 'Status tugas tidak dapat diperbarui.');
                }
                window.location.reload();
            }).catch((error) => {
                select.value = previousStatus;
                select.disabled = false;
                row.removeAttribute('aria-busy');
                savingNotice.hidden = true;
                showTaskNotice(error.message || 'Status tugas tidak dapat diperbarui. Silakan coba lagi.', 'error');
            });
        }
    });

    document.addEventListener('keydown', (event) => {
        if (modal.hidden) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            closeTaskModal();
        } else if (event.key === 'Tab') {
            const focusable = [...modal.querySelectorAll('button')];
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });

    const url = new URL(window.location.href);
    const requestedEditId = url.searchParams.get('edit');
    if (requestedEditId) {
        const row = [...document.querySelectorAll('[data-task-row]')].find((taskRow) => taskRow.dataset.taskId === requestedEditId);
        if (row) startEdit(row);
    }
    if (url.searchParams.has('edit') || url.searchParams.has('tambah')) {
        url.searchParams.delete('edit');
        url.searchParams.delete('tambah');
        window.history.replaceState(null, '', url);
    }
})();
</script>

<?php include 'includes/footer.php'; ?>
