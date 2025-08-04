<?php

namespace App\Exports;

use Modules\Member\Entities\Members ;
use Modules\Member\Entities\Units ;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RefExport implements FromCollection, WithHeadings, WithMapping
{
    public $filter = [];

    public function __construct($filter=[])
    {
        $this->filter = $filter;
    }

   
    public function map($member): array
    {
        return [
            'id'=>$member->id,
            'title'=>$member->title,
            'name'=>$member->name,
            'last_name'=>$member->last_name,
            'nickname'=>$member->nickname,
            'gender'=>$member->gender,
            'birth_day'=>$member->birth_day,
            'age'=>$member->age,
            'job'=>$member->job,
            'address'=>$member->address,
            'moo'=>$member->moo,
            'district'=>$member->district,
            'comunity'=>(!empty($member->community))?$member->community->name:'',
            'community_id'=>$member->community_id,
            'area_id'=>(!empty($member->area))?$member->area->name:'',
            'unit_id'=>$member->unit_id,
            'unit_name'=>(!empty($member->unit_name))?Units::where('unit_no',$member->unit_id)->where('area_id',$member->area_id)->first()->name:'',
            'choose_status'=>$member->choose_status,
            'personal_id'=>$member->personal_id,
            'mobile'=>$member->mobile,
            'status'=>$member->status,
            'aumper'=>$member->aumper,
            'province'=>$member->province,
            'bann'=>$member->bann,
            'bann_tub'=>$member->bann_tub,
            'year'=>$member->year,
            'section'=>$member->section,
            'unit'=>$member->unit,
            'location'=>$member->location,
            'bann_tub_dec'=>$member->bann_tub_dec,
            'ref_id'=>$member->ref_id,
            'other_ref'=>$member->other_ref,
            '_lft'=>$member->_lft,
            '_rgt'=>$member->_rgt,
            'parent_id'=>$member->parent_id,
            'updated_by'=>(!empty($member->update_user))?$member->update_user->name:'',
            'created_by'=>(!empty($member->create_user))?$member->create_user->name:'',
            'updated_at'=>$member->updated_at,
            'created_at'=>$member->created_at,
            'downline_count'=>$member->downline_count,
            'samehouse_count'=>$member->samehouse_count,
            'downline_100_count'=>$member->downline_100_count,
            'downline_50_count'=>$member->downline_50_count,
            'downline_20_count'=>$member->downline_20_count,
            'downline_0_count'=>$member->downline_0_count,
         ]; 
     }

    public function headings(): array
    {
        return [
            'id',
            'title'=>'คำนำหน้า',
            'name'=>'ชื่อ',
            'last_name'=>'สกุล',
            'nickname'=>'ชื่อเล่น',
            'gender'=>'เพศ',
            'birth_day'=>'วันเกิด',
            'age'=>'อายุ',
            'job'=>'อาชีพ',
            'address'=>'ที่อยู่',
            'moo'=>'หมู่',
            'district'=>'ตำบล',
            'comunity'=>'ชุมชน',
            'community_id'=>'ชุมชน id',
            'area_id'=>'เขตเลือกตั้ง',
            'unit_id'=>'หน่วย',
            'unit_name'=>'หน่วยชื่อ',
            'choose_status'=>'ความมั่นใจ',
            'personal_id'=>'เลยที่บัตร ปปช',
            'mobile'=>'โทร',
            'status'=>'สถานะ',
            'aumper'=>'อำเภอ (เก่า)',
            'province'=>'จังหวัด (เก่า)',
            'bann'=>'บ้าน',
            'bann_tub'=>'บ้านทับ',
            'year'=>'ปี',
            'section'=>'เขต (เก่า)',
            'unit'=>'หน่วย (เก่า)',
            'location'=>'พื้นที่',
            'bann_tub_dec'=>'บ้านทับ',
            'ref_id'=>'id ผู้แนะนำ',
            'other_ref'=>'id ผู้แนะนำอื่น',
            '_lft',
            '_rgt',
            'parent_id',
            'updated_by'=>'id ผู้อัพเดท',
            'created_by'=>'id ผู้สร้าง',
            'updated_at'=>'วันที่อัพเดท',
            'created_at'=>'วันที่สร้าง',
            'downline_count'=>'จำนวนคนแนะนำ',
            'samehouse_count'=>'จำนวนคนในบ้าน',
            'downline_100_count'=>'จำนวนคนแนะนำ (100)',
            'downline_50_count'=>'จำนวนคนแนะนำ (50)',
            'downline_20_count'=>'จำนวนคนแนะนำ (20)',
            'downline_0_count'=>'จำนวนคนแนะนำ (0)',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        if(!empty($this->filter)){


            $o_member = new Members;
        
            // if(!empty( $this->filter['parent_id'] )){
            //     $ref_id = $this->filter['parent_id'] ;
            //     $o_member = $o_member->Where(function ($query) use($ref_id) {
            //         $query->orWhere('ref_id', ref_id) ;
            //           // ->orWhere('other_ref','like', '%'.$this->filter['member_id'].',%');
            //     }) ;
            // }

            if(!empty( $this->filter['district'])){
                $o_member = $o_member->where('district', $this->filter['district']);
            }

            if(!empty( $this->filter['moo'])){
                $o_member = $o_member->where('moo', $this->filter['moo']);
            }

            if(!empty( $this->filter['address'])){
                $o_member = $o_member->where('address','like', $this->filter['address']);
            }

            if(!empty( $this->filter['area_id'])){
                $o_member = $o_member->where('area_id', $this->filter['area_id']);
            }

            if(!empty( $this->filter['community_id'])){
                $o_member = $o_member->where('community_id',$this->filter['community_id']);
            }

            if(!empty( $this->filter['choose_status'])){
                if($this->filter['choose_status']==1){
                    $o_member = $o_member->where('choose_status','0');
                }elseif($this->filter['choose_status']==2){
                    $o_member = $o_member->where('choose_status','>=','20');
                }else{
                    $o_member = $o_member->where('choose_status', $this->filter['choose_status']);
                }
                
            }

            if(!empty( $this->filter['has_ref'])){
                $o_member = $o_member->having('downline_count', '>', 0);
            }

            if(!empty( $this->filter['parent_id'])){
                if(empty($this->filter['children'])){
                    $o_member = $o_member->where('ref_id',$this->filter['parent_id']);
                }else{
                    $o_member = $o_member->whereIn('id',$this->filter['children']);
                }
            }


            $o_member = $o_member->with('area')->with('unit_name')->with('community')->with('create_user')->with('update_user')->withcount('downline')->withcount('samehouse')->withcount('downline_100')->withcount('downline_50')->withcount('downline_20')->withcount('downline_0')   ;

            $members  =  $o_member->get();
            return $members ;
        }else{
            return Members::with('area')->with('unit_name')->with('community')->with('create_user')->with('update_user')->withcount('downline')->withcount('samehouse')->withcount('downline_100')->withcount('downline_50')->withcount('downline_20')->withcount('downline_0')->all() ;
        }


        
    }
}
