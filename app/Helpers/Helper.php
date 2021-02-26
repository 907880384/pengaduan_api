<?php

namespace App\Helpers;

use App\Notifications\NotifikasiComplaint as NotificationComplaint;
use App\Notifications\AssignedNotif as NotificationAssignedComplaint;
use App\Notifications\AssignedWorkingComplaint as NotificationAssignedWorkingComplaint;
use Notification;

class Helper {
    
    public static function messageResponse() {
        return (object) [
            'NOT_ACCESSED' => 'Mohon maaf anda tidak dapat mengakses perintah ini',
            'COMPLAINT_NOT_FOUND' => 'Informasi pengaduan yang anda cari tidak ditemukan',
            'COMPLAINT_DELETE_SUCCESS' => 'Data pengaduan berhasil di hapus',
            'COMPLAINT_DELETE_FAIL' => 'Data pengaduan gagal di hapus',
            'COMPLAINT_CREATE_SUCCESS' => 'Data pengaduan baru berhasil ditambahkan',
            'COMPLAINT_CREATE_FAIL' => 'Data pengaduan baru gagal disimpan',
            'COMPLAINT_BEFORE_ASSIGNED' => 'Data pengaduan sudah pernah disetujui',
            'COMPLAINT_ASSIGNED' => 'Data pengaduan berhasil disetujui admin',
            'COMPLAINT_ASSIGNED_FAIL' => 'Data pengaduan gagal disetujui',
            'COMPLAINT_WORK_ACCEPTED_FAIL' => 'Permintaan pelaksanaan penggaduan ditolak',
            'COMPLAINT_WORK_ACCEPTED' => 'Permintaan pelaksanaan penggaduan diterima',
            'NOTIFICATION_ADD_FAIL' => 'Info notifikasi gagal ditambahkan',
            'NOTIFICATION_NOT_FOUND' => 'Info notifikasi tidak ditemukan',
            'FILE_NOT_EXIST' => 'File yang diupload kosong',
            'COMPLAINT_FINISHED' => 'Pekerjaan telah selesai dan Laporan hasil pekerjaan berhasil dikirim',
            'COMPLAINT_FINISHED_FAIL' => 'Konfirmasi pekerjaan selesai ditolak',
            'PROFILE_UPDATE' => 'Profil anda berhasil diperbarui',
            'PROFILE_CREATE' => 'Profil anda berhasil disimpan',
            'PROFILE_FAILED' => 'Profil gagal disimpan',
            'USER_INFO_FAILED' => 'Informasi pengguna tidak ditemukan',
            'PASSWORD_CHANGE_SUCCESS' => 'Password berhasil diubah',
            'PASSWORD_CHANGE_FAILED' => 'Password gagal diubah',
            
            'PRODUCT_CREATE_SUCCESS' => 'Data barang baru berhasil disimpan',
            'PRODUCT_UPDATE_SUCCESS' => 'Data barang berhasil diperbarui',
            'PRODUCT_CREATE_FAILED' => 'Data barang baru gagal disimpan',
            'PRODUCT_UPDATE_FAILED' => 'Data barang gagal diperbarui',
        ];
    }


    public static function setNotification($type, $receive, $result)
    {
        switch ($type) {
            case 'ASSIGNED_COMPLAINT':
                $user = \App\User::find($receive);
                Notification::send($user, new NotificationAssignedComplaint($result));
                break;
            
            case 'CREATE_COMPLAINT':
                $user = \App\User::find($receive);
                Notification::send($user, new NotificationComplaint($result));
                break;

            case 'START_WORKING_ASSIGNED':
                $user = \App\User::whereIn('id', $receive)->get();
                Notification::send($user, new NotificationAssignedWorkingComplaint($result));
            default:
                break;
        }
    }

    

}