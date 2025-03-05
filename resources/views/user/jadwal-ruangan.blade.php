@extends('layouts.user')
@section('title', 'Jadwal Ruangan')

@section('content')
    <div class="container mt-2">
        <h1>Jadwal Ruangan</h1>
        <div id="calendar" style="width: 100%; height: 100vh;"></div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Modal for Viewing Event Details -->
    <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventDetailModalLabel">Jadwal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Kegiatan : </strong> <span id="eventTitle"></span></p>
                    <p><strong>Mulai : </strong> <span id="eventStart"></span></p>
                    <p><strong>Selesai : </strong> <span id="eventEnd"></span></p>
                    <p><strong>Keterangan : </strong> <span id="eventDescription"></span></p>
                    <p><strong>Ruangan : </strong> <span id="eventColor"></span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Booking Room -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">Pinjam Ruangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('bookings.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="kegiatan">Nama Kegiatan</label>
                            <input type="text" class="form-control" id="kegiatan" name="kegiatan" required>
                        </div>
                        <div class="form-group">
                            <label for="start_time">Tanggal Mulai</label>
                            <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                        </div>
                        <div class="form-group">
                            <label for="end_time">Tanggal Selesai</label>
                            <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
                        </div>
                        <div class="form-group">
                            <label for="room_id">Ruangan</label>
                            <select required class="form-control" id="room_id" name="room_id">
                                <option value="" disabled selected>Pilih Ruangan</option>
                                @foreach($rooms as $ruangan)
                                    <option value="{{ $ruangan->id }}">{{ $ruangan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Ajukan Pinjam Ruangan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var jadwals = {!! json_encode($jadwals) !!};

            var colorToRoom = {
                '#041e42': 'Ruang Gereja',
                '#b2cae4': 'Konsistori',
                '#bab49e': 'Kantor Pendeta',
                '#b26801': 'Gedung Serba Guna',
                '#008080': 'Aula',
                '#d4af37': 'Kantor Tata Usaha',
                '#ff7f50': 'Ruang Sekolah Minggu',
                '#007f4e': 'Ruang Remaja dan Naposo'
            };

            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                initialView: 'dayGridMonth',
                events: jadwals,
                editable: false,

                // Klik pada event -> tampilkan detail event
                eventClick: function(info) {
                    $('#eventTitle').text(info.event.title);
                    var start = new Date(info.event.start);
                    var end = info.event.end ? new Date(info.event.end) : null;
                    var options = {
                        day: '2-digit', month: '2-digit', year: 'numeric',
                        hour: '2-digit', minute: '2-digit', hour12: false
                    };

                    $('#eventStart').text(start.toLocaleString('en-GB', options));
                    $('#eventEnd').text(end ? end.toLocaleString('en-GB', options) : '-');
                    $('#eventDescription').text(info.event.extendedProps.description || '-');

                    var eventColor = (info.event.backgroundColor || info.event.extendedProps.color || info.event.color);
                    var roomName = colorToRoom[eventColor] || '-';
                    $('#eventColor').text(roomName);

                    $('#eventDetailModal').modal('show');
                },

                // Klik pada tanggal kosong -> tampilkan form pemesanan
                dateClick: function(info) {
                    $('#start').val(info.dateStr + 'T08:00'); // Default jam 08:00
                    $('#end').val(info.dateStr + 'T10:00'); // Default jam 10:00
                    $('#bookingModal').modal('show');
                },
            });

            calendar.render();
        });
    </script>
@endsection
