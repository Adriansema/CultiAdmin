namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticController extends Controller
{
    public function index()
    {
        $visits = DB::cultivasena('visits')
            ->select(DB::raw('COUNT(id) as count, DATE(created_at) as date'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $registrations = DB::table('users') // Si tu tabla de usuarios tiene otro nombre, cámbialo aquí
            ->select(DB::raw('COUNT(id) as count, DATE(created_at) as date'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return response()->json([
            'visits' => $visits,
            'registrations' => $registrations
        ]);
    }
}


