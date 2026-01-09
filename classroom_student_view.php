    <?php
    require "db/db.php";
    $mydb = new myDB();

    include_once 'partials/session.php'
        ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>JOLNHS | Attendance</title>
        <link rel="icon" href="assets/images/system_image/school-logo.png" type="image/png">
        <link rel="stylesheet" href="assets/css/main.css">
        <script type="text/javascript" src="assets/js/jquery.min.js"></script>
        <script src="assets/js/sweetalert2.min.js"></script>
    </head>

    <body>

        <div class="container flex flex-col jus-start ">

            <!-- Header -->
            <?php include 'partials/header.php'; ?>

            <!-- Overlay -->
            <div id="overlay" style="display:none;"></div>

            <!-- Main Content -->
            <div class="content dashboard">

                <div class="flex al-center jus-around gap-50 attendance-summary" style="order: 2;">
                    <div class="box present">
                        <div class="box-label">Total Present</div>
                        <div class="box-value" id="totalPresent">0</div>
                    </div>
                    <div class="box absent">
                        <div class="box-label">Total Absent</div>
                        <div class="box-value" id="totalAbsent">0</div>
                    </div>
                    <div class="box late">
                        <div class="box-label">Total Late</div>
                        <div class="box-value" id="totalLate">0</div>
                    </div>
                    <div class="box excused">
                        <div class="box-label">Total Excused</div>
                        <div class="box-value" id="totalExcused">0</div>
                    </div>
                </div>

                <div class="top-controls flex jus-between al-center pt10 pb10">
                    <div class="flex al-center jus center gap-10">
                        <input type="date" name="attendanceDate" id="attendanceDate">
                    </div>
                </div>


                <div class="attendance-calendar">

                    <!-- THE CALENDAR WILL BE GENERATED HERE -->

                </div>


            </div>

        </div>



        <script src="assets/js/main.js"></script>

        <script>
            const CLASS_ID = new URLSearchParams(window.location.search).get("id");
            const TODAY = new Date();


            $(document).ready(function () {
                const today = new Date();
                const month = today.toISOString().slice(0, 7); // YYYY-MM
                $("#attendanceDate").val(month + "-01");

                loadStudentAttendance(month);
            });

            $("#attendanceDate").on("change", function () {
                const selectedMonth = this.value.slice(0, 7);
                loadStudentAttendance(selectedMonth);
            });


            function loadStudentAttendance(month) {

                $.ajax({
                    url: "db/request.php",
                    type: "POST",
                    data: {
                        action: "getStudentAttendance",
                        class_id: CLASS_ID,
                        month: month
                    },
                    dataType: "json",
                    success: function (res) {
                        if (res.status !== "success") {
                            Swal.fire("Error", res.message, "error");
                            return;
                        }

                        // Render calendar
                        renderCalendar(month, res.data);

                        // Update totals
                        $("#totalPresent").text(res.totals.total_present || 0);
                        $("#totalAbsent").text(res.totals.total_absent || 0);
                        $("#totalLate").text(res.totals.total_late || 0);
                        $("#totalExcused").text(res.totals.total_excuse || 0);
                    }

                });
            }


            function renderCalendar(month, attendanceData) {

                const calendar = $(".attendance-calendar");
                calendar.html("");

                const [year, mon] = month.split("-");
                const daysInMonth = new Date(year, mon, 0).getDate();

                // Build lookup map (FIXED)
                const attendanceMap = {};
                attendanceData.forEach(a => {
                    attendanceMap[a.date] = a.status;   // ✅ correct field
                });

                for (let day = 1; day <= daysInMonth; day++) {

                    const dateStr = `${month}-${String(day).padStart(2, "0")}`;
                    const status = attendanceMap[dateStr];

                    let colorClass = "no-record";

                    if (status === "present") colorClass = "present";
                    else if (status === "absent") colorClass = "absent";
                    else if (status === "late") colorClass = "late";
                    else if (status === "excuse") colorClass = "excused";

                    calendar.append(`
                        <div class="calendar-day ${colorClass}">
                            <span class="day-number">${day}</span>
                        </div>
                    `);
                }
            }

        </script>


    
    </body>

    </html>