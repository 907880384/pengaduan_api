<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\TypeIdentity;
use App\Models\FileVisitor;
use App\User;

use Auth;
use Helper;
use Storage;
use File;
use DataTables;

class VisitorController extends Controller
{
    private function sendResponse($msg, $status=200) {
        return response(['message' => $msg], $status);
    }

    public function index()
    {
        return view('pages.buku_tamu.index');
    }


    public function listVisitors(Request $req) {
        $user = Auth::user();
        $isDone = $req->query('isDone') == 'false' ? false : true;
        
        $records = Visitor::with([
            'user',
            'fileVisitors',
            'typeIdentity'
        ]);

        $records->where('selesai', $isDone);

        if($req->query('timeIn') == null) {
            $records->whereDate('time_masuk', '=', \Carbon\Carbon::today());
        }
        else {
            $records->whereDate('time_masuk', '=', \Carbon\Carbon::parse($req->query('timeIn'))->format('Y-m-d'));
        }

        $records->orderBy('time_masuk', 'desc')->get();

        return Datatables::of($records)->addIndexColumn()
            ->addColumn('reqvar', function($row) use($req) {
                return $req->query('timeIn');
            })
            ->addColumn('action', function($row) use($user) {
                $str = '';

                $str .= '<a href="'. url('visitors/'. $row->id) .'" class="btn btn-info btn-sm"> <i class="fas fa-eye"></i> DETAIL</a> &nbsp;';
                
                if(!$row->selesai && strtolower($user->roles()->first()->slug) != 'admin') {
                    $str .= '<button type="button" class="btn btn-success btn-sm" onclick="setDone('.$row->id.')"><i class="fas fa-check"></i> SELESAI</button>&nbsp;';
                }

                if($user->roles()->first()->slug == 'admin') {
                    $str .= '<button type="button" class="btn btn-danger btn-sm" onclick="deleteRow('.$row->id.')"><i class="fas fa-trash"></i> HAPUS
                    </button>';
                }
                    
                return $str;
            })->rawColumns(['action'])->make(true);

    }

    public function create()
    {
        $typeIdentities = TypeIdentity::get();
        return view('pages.buku_tamu.create', compact('typeIdentities'));
    }

    public function store(Request $req)
    {
        $user = Auth::user();

        $req->validate([
            "nama" => "required",
            "tipe_identitas" => "required",
            "no_identitas" => "required",
            "no_hp" => "required",
            "tujuan" => "required",
            "dataImage" => "required",
        ]);

        if($user->roles()->first()->slug != "receptionis") {
            return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);
        }

        $dataImage = json_decode($req->dataImage, true);

        $visitor = Visitor::create([
            'nama' => $req->nama,
            'no_identitas' => $req->no_identitas,
            'tipe_identitas' => $req->tipe_identitas,
            'no_hp' => $req->no_hp,
            'tujuan' => $req->tujuan,
            'keterangan' => $req->keterangan,
            'time_masuk' => \Carbon\Carbon::now(new \DateTimeZone('Asia/Jakarta'))->toDateTimeString(),
            'user_id' => $user->id
        ]);


        if(!$visitor) {
            return $this->sendResponse(Helper::messageResponse()->CREATE_VISITOR_FAILED, 400); 
        }
        
        $dataFile = [];

        if(count($dataImage) > 0) {
            foreach ($dataImage as $key => $value) {
                $path = public_path('storage/buku_tamu');

                $image = Helper::convertWebcameImage($value['uri']);
                $filename = 'image_'.$visitor->id.'_'.$value['id'].'_'. $key.'.jpg';
                $filepath = 'storage/buku_tamu/' . $filename;

                if(!File::isDirectory($path)){
                    File::makeDirectory($path, 0777, true, true);
                }
    
                $dataFile[] = [
                    'visitor_id' => $visitor->id,
                    'filename' => $filename,
                    'filepath' => File::put($filepath, $image) ? $filepath : '',
                    'created_at' => \Carbon\Carbon::now(new \DateTimeZone('Asia/Jakarta'))->toDateTimeString(),
                    'updated_at' => \Carbon\Carbon::now(new \DateTimeZone('Asia/Jakarta'))->toDateTimeString(),
                ];
    
            }
    
            if(count($dataFile) > 0) {
                FileVisitor::insert($dataFile);
            }
        }

        return response([
            'message' => Helper::messageResponse()->CREATE_VISITOR_SUCCESS,
            'data' => $dataFile
        ], 200);
        
    }

    public function show($id)
    {
        $visitor = Visitor::with([
            'user',
            'fileVisitors',
            'typeIdentity'
        ])->find($id);
        
        // return $visitor;
        return view('pages.buku_tamu.detail', compact('visitor'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function exitVisitor($id) {
        $user = Auth::user();

        if($user->roles()->first()->slug != "receptionis") {
            return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);
        }

        $visitor = Visitor::find($id);
        $visitor->selesai = true;
        $visitor->time_keluar = \Carbon\Carbon::now(new \DateTimeZone('Asia/Jakarta'))->toDateTimeString();
        
        if(!$visitor->save()) {
            return $this->sendResponse('Opps, update data status tamu gagal', 400);
            
        }

        return $this->sendResponse(Helper::messageResponse()->EXITED_VISITOR_SUCCESS, 200);
    }
}
