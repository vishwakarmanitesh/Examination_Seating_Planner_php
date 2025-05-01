<?php include('head.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>
<style>
    table {
        border-collapse: collapse;
        width: 100%;
        max-width: 800px;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 6px;
    }

    th {
        background-color: #f8f9fa;
        font-weight: bold;
    }
</style>

<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary">Generate Seating Plan</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">Upload Files</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8" style="margin-left: 10%;">
                <div class="card">
                    <div class="card-body">
                        <form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
                            <input type="hidden" name="currnt_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">

                            <div class="form-group">
                                <label>Upload BCA File</label>
                                <input type="file" class="form-control" name="csvFile1" accept=".csv" required>
                            </div>

                            <div class="form-group">
                                <label>Upload MCA File</label>
                                <input type="file" class="form-control" name="csvFile2" accept=".csv" required>
                            </div>

                            <button type="submit" name="btn_save" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>

                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (
                        isset($_FILES['csvFile1']) && $_FILES['csvFile1']['error'] === UPLOAD_ERR_OK &&
                        isset($_FILES['csvFile2']) && $_FILES['csvFile2']['error'] === UPLOAD_ERR_OK
                    ) {
                        $uploadedFile1 = $_FILES['csvFile1']['tmp_name'];
                        $uploadedFile2 = $_FILES['csvFile2']['tmp_name'];
                        $columnIndex = 1; // 2nd column

                        function extractColumnFromCSV($csvFile, $columnIndex)
                        {
                            $dataArray = [];
                            $rowCount = 0;

                            if (($handle = fopen($csvFile, 'r')) !== false) {
                                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                                    $rowCount++;
                                    if ($rowCount <= 2) continue;
                                    if (isset($data[$columnIndex])) {
                                        $dataArray[] = trim($data[$columnIndex]);
                                    }
                                }
                                fclose($handle);
                            } else {
                                throw new Exception("Unable to open the file: $csvFile.");
                            }

                            return $dataArray;
                        }

                        function writeArrayToCSV($array, $filePath)
                        {
                            if (($handle = fopen($filePath, 'w')) !== false) {
                                foreach ($array as $row) {
                                    fputcsv($handle, $row);
                                }
                                fclose($handle);
                            } else {
                                throw new Exception("Unable to write to file: $filePath");
                            }
                        }

                        try {
                            $bca = extractColumnFromCSV($uploadedFile1, $columnIndex); // BCA students
                            $mca = extractColumnFromCSV($uploadedFile2, $columnIndex); // MCA students

                            // 3 rooms with 8 rows and 6 columns each
                            $e1 = array_fill(0, 8, array_fill(0, 6, ''));
                            $e2 = array_fill(0, 8, array_fill(0, 6, ''));
                            $e3 = array_fill(0, 8, array_fill(0, 6, ''));
                            $rooms = [&$e1, &$e2, &$e3];

                            $b = 0; // Index for BCA
                            $m = 0; // Index for MCA
                            $count = 0;

                            foreach ($rooms as &$room) {
                                for ($i = 0; $i < 8; $i++) {
                                    for ($j = 0; $j < 6; $j++) {
                                        if ($count >= 144) break 2;

                                        // Alternate BCA and MCA based on column index
                                        if (in_array($j, [0, 2, 3, 5])) {
                                            $room[$i][$j] = $bca[$b] ?? '';
                                            $b++;
                                        } else {
                                            $room[$i][$j] = $mca[$m] ?? '';
                                            $m++;
                                        }

                                        $count++;
                                    }
                                }
                            }

                            // Write to CSV (optional)
                            writeArrayToCSV($e1, 'e1.csv');
                            writeArrayToCSV($e2, 'e2.csv');
                            writeArrayToCSV($e3, 'e3.csv');

                            echo "<div class='alert alert-success mt-3'>Seating plan generated successfully and saved as <strong>e1.csv, e2.csv, e3.csv</strong>.</div>";

                            function displayRoom($room, $title)
                            {
                                echo "<h4 class='mt-4'>$title</h4>";
                                echo "<div style='overflow-x:auto; margin-bottom: 20px;'>";
                                echo "<table class='table table-bordered table-sm text-center'>";
                                echo "<thead><tr>";
                                for ($i = 0; $i < 6; $i++) {
                                    echo "<th>" . ($i % 2 == 0 ? "BCA" : "MCA") . "</th>";
                                }
                                echo "</tr></thead><tbody>";
                                foreach ($room as $row) {
                                    echo "<tr>";
                                    foreach ($row as $seat) {
                                        echo "<td>" . htmlspecialchars($seat) . "</td>";
                                    }
                                    echo "</tr>";
                                }
                                echo "</tbody></table></div>";
                            }

                            displayRoom($e1, "Room E1");
                            displayRoom($e2, "Room E2");
                            displayRoom($e3, "Room E3");
                        } catch (Exception $e) {
                            echo "<div class='alert alert-danger mt-3'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger mt-3'>Please upload both CSV files.</div>";
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>