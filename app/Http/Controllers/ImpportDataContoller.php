<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImpportDataContoller extends Controller
{
    public function importData(Request $req)
    {
        $file = $req->file('upload');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        foreach ($rows as $index => $row) {
            if ($index == 0) {
                continue; // Skip header row
            }
            User::updateOrCreate(
                [
                    'name' => $row[1],
                    'username' => $row[2],
                    'kelas' => $row[4],
                    'password' => bcrypt($row[5]),
                    'status' => 'aktif',
                ]
            );
        }

        return redirect()->back()->with('success', 'Data imported successfully');
    }

}

// $req->validate([
//     'upload' => 'required|file|mimes:xls,xlsx',
// ]);
//
// $indexKe1 = 1;
//
// $file = $req->file('upload');
// $spreadsheet = IOFactory::load($file->getPathName());
// $sheet = $spreadsheet->getActiveSheet();
//
// $rows = [];
// $data = [];
//
// foreach ($sheet->getRowIterator() as $row) {
//     $cellIterator = $row->getCellIterator();
//     $cellIterator->setIterateOnlyExistingCells(false);
//
//     $cells = [];
//     foreach ($cellIterator as $cell) {
//         $cells[] = $cell->getValue();
//     }
//     $rows[] = $cells;
// }
//
// $status = 'active';
//
// foreach ($rows as $row) {
//     if ($indexKe1 > 1) { // Skip the first two rows (index 0 and 1)
//         $record = [
//             'name' => !empty($row[1]) ? $row[1] : '', // 'name'
//             'username' => !empty($row[2]) ? $row[2] : '', // 'username'
//             'kelas' => !empty($row[4]) ? $row[4] : '', // 'kelas'
//             'password' => !empty($row[5]) ? bcrypt($row[5]) : '', // 'password'
//             'status' => !empty($status) ? $status : '',
//         ];
//         $data[] = $record;
//     }
//     $indexKe1++;
// }
//
// foreach ($data as $record) {
//     User::create($record);
// }
// // dd($data);
//
// return redirect()->route('tambah-user')->with('success', 'User created successfully');