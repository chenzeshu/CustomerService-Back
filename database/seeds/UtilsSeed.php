<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Generator as Faker;
class UtilsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        DB::table('professions')->insert([
            ['name'=>'水利'],
            ['name'=>'环保'],
            ['name'=>'消防'],
            ['name'=>'电力'],
            ['name'=>'安监'],
            ['name'=>'民防'],
            ['name'=>'通信'],
            ['name'=>'气象'],
            ['name'=>'疾控'],
            ['name'=>'电子'],
            ['name'=>'酒店'],
            ['name'=>'政府'],
            ['name'=>'防汛'],
            ['name'=>'信息技术'],
            ['name'=>'公安'],
            ['name'=>'研究院'],
            ['name'=>'部队'],
            ['name'=>'学校'],
            ['name'=>'改装厂'],
            ['name'=>'地址'],
            ['name'=>'科技'],
            ['name'=>'监测站'],
            ['name'=>'系统集成'],
            ['name'=>'建设'],
            ['name'=>'广电'],
            ['name'=>'医院'],
            ['name'=>'广播'],
            ['name'=>'投资'],
            ['name'=>'救援队'],
            ['name'=>'开发商'],
            ['name'=>'汽车'],
            ['name'=>'防汛'],
            ['name'=>'报社'],
            ['name'=>'服务'],
            ['name'=>'保护'],
            ['name'=>'应急办'],
        ]);

        DB::table('coors')->insert([
            ['name'=>$faker->company, 'address'=> $faker->address, 'phone'=>$faker->phoneNumber],
            ['name'=>$faker->company, 'address'=> $faker->address, 'phone'=>$faker->phoneNumber],
            ['name'=>$faker->company, 'address'=> $faker->address, 'phone'=>$faker->phoneNumber],
            ['name'=>$faker->company, 'address'=> $faker->address, 'phone'=>$faker->phoneNumber],
        ]);

        DB::table('contract_types')->insert([
            ['name'=>'集成'],
            ['name'=>'服务'],
            ['name'=>'综合'],//信道类型单独提出,在信道合同入口里做
        ]);

        DB::table('service_sources')->insert([
            ['name'=>'400电话'],
            ['name'=>'工程中心'],
            ['name'=>'营运中心'],
            ['name'=>'小程序']
        ]);
        DB::table('service_types')->insert([
            ['name'=>'故障处理'],
            ['name'=>'巡检'],
            ['name'=>'应急保障'],
            ['name'=>'远程协助'],
            ['name'=>'集成'],
            ['name'=>'其他']
        ]);

        factory(\App\Models\Doc::class, 100)->create();

        //服务套餐表
        DB::table('contract_planutils')->insert([
             ['name'=>'巡检', 'unit'=>'次/年', 'type'=>'有计划', 'type2'=>'普通'],
            ['name'=>'应急保障', 'unit'=>'人/天', 'type'=>'无计划', 'type2'=>'普通'],
            ['name'=>'培训', 'unit'=>'人/天', 'type'=>'无计划', 'type2'=>'普通'],
            ['name'=>'长期租赁','unit'=>'日', 'type'=>'无计划', 'type2'=>'其他'],
            ['name'=>'短期租赁', 'unit'=>'日', 'type'=>'无计划', 'type2'=>'其他'],
            ['name'=>'延长保修', 'unit'=> '年', 'type'=>'无计划', 'type2'=>'其他'],
            ['name'=>'设备维修', 'unit'=>'次', 'type'=>'无计划', 'type2'=>'普通'],
            ['name'=>'上门维修', 'unit'=>'次', 'type'=>'无计划', 'type2'=>'普通'],
            ['name'=>'升级改造', 'unit'=>'次', 'type'=>'无计划', 'type2'=>'普通'],
            ['name'=>'采购设备', 'unit'=>'元(人民币)', 'type'=>'无计划', 'type2'=>'财务'],
        ]);

        DB::table('id_records')->insert([
            ['record'=>'1'], ['record'=>'1'], ['record'=>'1'], ['record'=>'1'], ['record'=>'1'],
        ]);
    }
}
