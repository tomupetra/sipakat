@extends('layouts.admin')
@section('title', 'Jadwal Ruangan')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

@section('content')
    <div class="container mt-2">
        <h1>Jadwal Ruangan</h1>
        <div class="row">
            <div class="col-md-6">
                <div class="input-group mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search events">
                    <div class="input-group-append">
                        <button id="searchButton" class="btn btn-primary">{{ __('Search') }}</button>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="btn-group mb-3" role="group" aria-label="Calendar Actions">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                        data-bs-target="#addScheduleModal">
                        {{ __('Tambah Jadwal') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div id="calendar" style="width: 100%; height: 100vh; overflow-x: auto;"></div>
            </div>
        </div>
    </div>

    <!-- Modal for Viewing Schedule Details -->
    <div class="modal fade" id="viewScheduleModal" tabindex="-1" aria-labelledby="viewScheduleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewScheduleModalLabel">Detail Jadwal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Title:</strong> <span id="viewTitle"></span></p>
                    <p><strong>Start:</strong> <span id="viewStart"></span></p>
                    <p><strong>End:</strong> <span id="viewEnd"></span></p>
                    <p><strong>Description:</strong> <span id="viewDescription"></span></p>
                    <button type="button" class="btn btn-primary" id="editButton" data-bs-toggle="modal"
                        data-bs-target="#editScheduleModal">Edit</button>
                    <button type="button" class="btn btn-danger" id="deleteButton">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Editing Schedule -->
    <div class="modal fade" id="editScheduleModal" tabindex="-1" aria-labelledby="editScheduleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editScheduleModalLabel">Edit Jadwal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editEventForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editEventId" name="id">
                        <div class="form-group">
                            <label for='editTitle'>{{ __('Title') }}</label>
                            <input type='text' class='form-control' id='editTitle' name='title' required>
                        </div>

                        <div class="form-group">
                            <label for="editStart">{{ __('Start') }}</label>
                            <input type='datetime-local' class='form-control' id='editStart' name='start' required>
                        </div>

                        <div class="form-group">
                            <label for="editEnd">{{ __('End') }}</label>
                            <input type='datetime-local' class='form-control' id='editEnd' name='end' required>
                        </div>

                        <div class="form-group">
                            <label for="editDescription">{{ __('Description') }}</label>
                            <input type="text" id="editDescription" name="description" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-success">{{ __('Save Changes') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Adding Schedule -->
    <div class="modal fade" id="addScheduleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addScheduleModalLabel">Tambah Jadwal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="eventForm" action="{{ route('jadwal.create') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for='title'>{{ __('Title') }}</label>
                            <input type='text' class='form-control' id='title' name='title' required>
                        </div>

                        <div class="form-group">
                            <label for="start">{{ __('Start') }}</label>
                            <input type='datetime-local' class='form-control' id='start' name='start' required
                                value='{{ now()->format('Y-m-d\TH:i') }}'>
                        </div>

                        <div class="form-group">
                            <label for="end">{{ __('End') }}</label>
                            <input type='datetime-local' class='form-control' id='end' name='end' required
                                value='{{ now()->format('Y-m-d\TH:i') }}'>
                        </div>

                        <div class="form-group">
                            <label for="description">{{ __('Description') }}</label>
                            <input type="text" id="description" name="description" class="form-control"
                                rows="3"></input>
                        </div>

                        <div class="form-group">
                            <label for="color">{{ __('Ruangan') }}</label>
                            <select required class="form-control" id="color" name="color">
                                <option value="" disabled selected>Pilih Ruangan</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->color }}">{{ $room->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom CSS to increase the size of event nodes */
        .fc-event {
            font-size: 1em;
            /* Increase font size */
            padding: 5px;
            /* Increase padding */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone-with-data.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var calendarEl = document.getElementById('calendar');
            if (!calendarEl) {
                console.error('Calendar element not found');
                return;
            }

            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                initialView: 'dayGridMonth',
                timeZone: 'Asia/Jakarta', // Use local computer timezone
                events: '/api/events',
                editable: true, // Ensure events are editable
                eventClick: function(info) {
                    $('#viewTitle').text(info.event.title);
                    $('#viewStart').text(moment(info.event.start).format('YYYY-MM-DD HH:mm'));
                    $('#viewEnd').text(moment(info.event.end).format('YYYY-MM-DD HH:mm'));
                    $('#viewDescription').text(info.event.extendedProps.description || '-');
                    $('#editEventId').val(info.event.id);
                    $('#editTitle').val(info.event.title);
                    $('#editStart').val(moment(info.event.start).format('YYYY-MM-DDTHH:mm'));
                    $('#editEnd').val(moment(info.event.end).format('YYYY-MM-DDTHH:mm'));
                    $('#editDescription').val(info.event.extendedProps.description || '-');
                    $('#viewScheduleModal').modal('show');
                },
                eventContent: function(info) {
                    var eventTitle = info.event.title;
                    var eventElement = document.createElement('div');
                    eventElement.innerHTML = eventTitle;

                    return {
                        domNodes: [eventElement]
                    };
                },
                eventDidMount: function(info) {
                    if (info.event.extendedProps.color) {
                        info.el.style.backgroundColor = info.event.extendedProps.color;
                        info.el.style.borderColor = info.event.extendedProps.color;
                        info.el.style.color = "#ffffff";
                    }
                },
                eventDrop: function(info) {
                    var eventId = info.event.id;
                    var newStartDate = info.event.start;
                    var newEndDate = info.event.end || newStartDate;

                    $.ajax({
                        url: `/admin/ruangan/update/${eventId}`,
                        method: 'PUT',
                        data: {
                            '_token': "{{ csrf_token() }}",
                            start: moment.tz(newStartDate, 'Asia/Jakarta').format(
                                'YYYY-MM-DDTHH:mm:ssZ'),
                            end: moment.tz(newEndDate, 'Asia/Jakarta').format(
                                'YYYY-MM-DDTHH:mm:ssZ'),
                        },
                        success: function() {
                            console.log('Event moved successfully.');
                            calendar.refetchEvents();
                        },
                        error: function(error) {
                            console.error('Error moving event:', error);
                            alert('Gagal memindahkan jadwal.');
                        }
                    });
                },
                eventResize: function(info) {
                    updateEvent(info.event.id, info.event.start, info.event.end);
                }
            });

            calendar.render();

            // Form submission using AJAX
            $('#eventForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: `/create-schedule`,
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        // Close modal and refresh calendar
                        window.location.href = '/admin/ruangan/jadwal';
                        $('#addScheduleModal').modal('hide');
                        calendar.refetchEvents();
                        alert('Jadwal berhasil disimpan.');
                    },
                    error: function(xhr) {
                        console.error('Error saving schedule:', xhr.responseJSON.message);
                        alert('Gagal menyimpan jadwal. Silakan coba lagi.');
                    }
                });
            });

            // Handle delete button click
            $('#deleteButton').on('click', function() {
                var eventId = $('#editEventId').val();
                if (confirm("Apakah Anda yakin ingin menghapus jadwal ini?")) {
                    deleteEvent(eventId);
                }
            });

            // Form submission using AJAX for editing
            $('#editEventForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var eventId = $('#editEventId').val();

                $.ajax({
                    url: `/admin/ruangan/update/${eventId}`,
                    method: 'PUT',
                    data: formData,
                    success: function(response) {
                        $('#editScheduleModal').modal('hide');
                        calendar.refetchEvents();
                        alert('Jadwal berhasil diperbarui.');
                    },
                    error: function(xhr) {
                        console.error('Error updating schedule:', xhr.responseJSON.message);
                        alert('Gagal memperbarui jadwal. Silakan coba lagi.');
                    }
                });
            });

            // Delete event function
            function deleteEvent(eventId) {
                $.ajax({
                    url: `/admin/ruangan/delete/${eventId}`,
                    method: 'DELETE',
                    success: function() {
                        alert('Jadwal berhasil dihapus.');
                        calendar.refetchEvents();
                    },
                    error: function(xhr) {
                        console.error('Error deleting event:', xhr.responseJSON.message);
                        alert('Gagal menghapus jadwal. Silakan coba lagi.');
                    }
                });
            }

            // Update event function
            function updateEvent(eventId, start, end) {
                $.ajax({
                    url: `/admin/ruangan/${eventId}`,
                    method: 'PUT',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        start: moment.tz(start, 'Asia/Jakarta').format('YYYY-MM-DDTHH:mm:ssZ'),
                        end: moment.tz(end, 'Asia/Jakarta').format('YYYY-MM-DDTHH:mm:ssZ'),
                    },
                    success: function() {
                        console.log('Event updated successfully.');
                        calendar.refetchEvents();
                    },
                    error: function(error) {
                        console.error('Error updating event:', error);
                        alert('Gagal memperbarui jadwal.');
                    }
                });
            }
        });
    </script>
@endsection
