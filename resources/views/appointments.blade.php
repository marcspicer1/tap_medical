<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />

    </head>
    <body>
        <div class="container">

            <form method="get" action="/" class="mt-5">
                <div class="form-row float-right">
                    <div class="col-auto date">
                            <div class="input-group input-append date" id="datePicker">
                                <input type="text" class="form-control" value="{{ $filter_date }}" " name="start_date" required/>
                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                    </div>
                    <div class="col-auto">
                        <div class="input-group mb-2">
                            <select name="doctor" class="form-control" required>
                                <option value="">Select Doctor</option>
                                @if($doctors)
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" @if($filter_doctor == $doctor->id) selected @endif >{{ $doctor->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary mb-2">Filter</button>
                    </div>
                    <div class="col-auto">
                        <button type="button" onclick="javascript:location.href='{{ url('/') }}'" class="btn btn-secondary mb-2">Reset</button>
                    </div>
                </div>
            </form>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">Doctor Name</th>
                    <th scope="col">Appointment Date</th>
                    <th scope="col">Speciality</th>
                    <th scope="col">Status</th>
                </tr>
                </thead>
                <tbody>
                @if(count($appointments))
                    @foreach($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->doctor->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($appointment->start_at)->isoFormat('MMMM Do YYYY @ h:mm a') }}</td>
                            <td>{{ $appointment->speciality->name }}</td>
                            <td>
                                @if(\Carbon\Carbon::now() > $appointment->start_at)
                                    <span class="badge badge-secondary">PAST</span>
                                @else
                                    <span class="badge badge-success">Upcoming</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="text-center">No appointments found.</td>
                    </tr>
                @endif

                </tbody>
            </table>
            <nav class="float-right">
                {{ $appointments->links() }}
{{--                <ul class="pagination">--}}
{{--                    <li class="page-item"><a class="page-link" href="#">Previous</a></li>--}}
{{--                    <li class="page-item"><a class="page-link" href="#">1</a></li>--}}
{{--                    <li class="page-item"><a class="page-link" href="#">2</a></li>--}}
{{--                    <li class="page-item"><a class="page-link" href="#">3</a></li>--}}
{{--                    <li class="page-item"><a class="page-link" href="#">Next</a></li>--}}
{{--                </ul>--}}
            </nav>
        </div>
        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <!-- Latest compiled JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#datePicker')
                    .datepicker({
                        format: 'yyyy-mm-dd'
                    })
            })
        </script>
    </body>
</html>
