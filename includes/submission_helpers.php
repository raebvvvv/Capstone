<?php


/**
 * Creates a new submission in the database
 */
function saveSubmission($pdo, $data, $files) {
    try {
        $pdo->beginTransaction();

        // Insert main submission record
        $submissionId = insertSubmissionRecord($pdo, $data);
        
        // Save uploaded documents
        saveSubmissionDocuments($pdo, $submissionId, $files);

        // Save co-authors if any
        if (!empty($data['authors'])) {
            saveCoAuthors($pdo, $submissionId, $data['authors']);
        }

        $pdo->commit();
        return $submissionId;

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Insert the main submission record
 */
function insertSubmissionRecord($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO submissions (
            user_id, first_name, last_name, student_number,
            home_address, mobile_number, webmail, campus,
            academic_level, college, program, work_classification,
            title, adviser, adviser_coauthor, date_accomplished,
            accepted_terms, status
        ) VALUES (
            :user_id, :first_name, :last_name, :student_number,
            :home_address, :mobile_number, :webmail, :campus,
            :academic_level, :college, :program, :work_classification,
            :title, :adviser, :adviser_coauthor, :date_accomplished,
            :accepted_terms, 'pending_review'
        )
    ");

    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'student_number' => $data['student_number'],
        'home_address' => $data['home_address'],
        'mobile_number' => $data['mobile_number'],
        'webmail' => $data['webmail'],
        'campus' => $data['campus'],
        'academic_level' => $data['academicLevel'],
        'college' => $data['college'],
        'program' => $data['program'],
        'work_classification' => $data['workClassification'],
        'title' => $data['title'],
        'adviser' => $data['adviser'] ?? '',
        'adviser_coauthor' => $data['adviser_coauthor'] ? 1 : 0,
        'date_accomplished' => $data['date_accomplished'],
        'accepted_terms' => $data['accepted_terms'] ? 1 : 0
    ]);

    return $pdo->lastInsertId();
}

/**
 * Save uploaded documents
 */
function saveSubmissionDocuments($pdo, $submissionId, $files) {
    $stmt = $pdo->prepare("
        INSERT INTO submission_documents (
            submission_id, doc_type, file_path,
            file_size, mime_type
        ) VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($files as $type => $filename) {
        $filepath = app_path('uploads/' . $filename);
        $filesize = filesize($filepath);
        $mimetype = mime_content_type($filepath);

        $stmt->execute([
            $submissionId,
            $type,
            $filename,
            $filesize,
            $mimetype
        ]);
    }
}

/**
 * Save co-authors
 */
function saveCoAuthors($pdo, $submissionId, $authors) {
    $stmt = $pdo->prepare("
        INSERT INTO submission_authors (
            submission_id, first_name, last_name,
            student_id, mobile, home_address, webmail,
            is_adviser
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($authors as $author) {
        $stmt->execute([
            $submissionId,
            $author['firstName'],
            $author['lastName'],
            $author['studentId'] ?? '',
            $author['mobile'] ?? '',
            $author['address'] ?? '',
            $author['email'] ?? '',
            isset($author['is_adviser']) ? 1 : 0
        ]);
    }
}