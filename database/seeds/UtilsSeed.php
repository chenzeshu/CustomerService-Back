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
    }
}
