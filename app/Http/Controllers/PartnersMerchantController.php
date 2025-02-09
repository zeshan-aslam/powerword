<?php

namespace App\Http\Controllers;

use App\Models\PartnerIpStat;
use App\Models\PartnerMerchant;
use App\Models\PartnerOwnerIp;
use App\Models\PartnerProgram;
use App\Models\PartnersAffiliate;
use App\Models\PartnersJoinPgm;
use App\Models\PartnersTranscation;
use App\Models\PartnerTextOld;
use App\Models\PartnersPoweredWordsTracking;
use App\Models\PartnersPoweredWordsMatch;
use App\Models\ProgramAffiliate;
use App\Models\PartnersAffiliatePages;
use App\Models\VerifyToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\DB;

class PartnersMerchantController extends Controller
{
    public function getPartners(Request $request)
    {
        $affiliate_key = $request->header('Api-Key');
        // to check this key belongs to which program affliate

        $multiWhere = array('affiliate_secretkey' => $affiliate_key, 'affiliate_status' => "approved" );

        //$partner_affliate = PartnersAffiliate::where('affiliate_secretkey',$affiliate_key)->first();
        $partner_affliate = PartnersAffiliate::where($multiWhere)->first();
        
        if ($partner_affliate) {

            try{
                $partners_poweredwords_tracking = PartnersPoweredWordsTracking::create([
                    'affiliateid' => $partner_affliate->affiliate_id,
                    'domain' => $_SERVER['HTTP_REFERER'],
                ]);
            }
            catch(Exception $e)
            {
                
            }
    

            // check if cache file exists for partner for today date  --affiliate_secretkey-af
            //  i.e  current date-1a2v3a4z5c6h7a8t9b0ots2c5ri00pt-af
            //  if file exists return json from file
            //  if file does not exists run process create a new cache file for today date and return response
            
            $filename = $affiliate_key.'--'.now()->toDateString().'-af.json';

            if (file_exists( public_path()."/affiliateFile/".$filename)){
                try
                {
                    $contents = File::get(public_path()."/affiliateFile/".$filename);

                    return response()->json(['data'=> json_decode($contents),'override' => $partner_affliate->link_override_value, 'trackid' => $partners_poweredwords_tracking->id ] ,201);

                }
                catch (Illuminate\Contracts\Filesystem\FileNotFoundException $exception)
                {
                    return response()->json('File text is Not Readable form', 404);
                }
            }
         
            /*
            // to get the  PartnersJoinPgm record  check the affliate id
            $partner_join_pgms = PartnersJoinPgm::where('joinpgm_affiliateid',$partner_affliate->affiliate_id )
            // to check the merchant_status is approved
            ->whereHas('merchant',function($query){
                $query->where('merchant_status','approved');
            })
            // to check the program_status is active
            ->whereHas('program',function($query){
                $query->where('program_status','active');
            })
               // to check the joinpgm_status is approved
            ->where('joinpgm_status','approved')->get();

            //var_dump($partner_join_pgms);

            // dd($partner_join_pgms);

            //  check if partner programs is not null
            if($partner_join_pgms ){

                $links =[];
                $search_worlds =[];
                $search_records =[];
                foreach($partner_join_pgms as $partner_join_pgm){
                    // we go to the partners_text_old table to get the link
                    // to check the text_status is active
                    if(!is_null($partner_join_pgm->program->text)){

                        if ($partner_join_pgm->program->text->text_status  =='active') {
                            // $link =  $partner_join_pgm->program->text->text_url;
                            $link = config('app.api_url')."/saveDetail?id=".$partner_join_pgm->program->text->text_id.'&key='.$affiliate_key;
                            // array_push($links, $new_link);
                            $search_world =   $partner_join_pgm->merchant->brands ?? $partner_join_pgm->merchant->brands;

                            if ($search_world !== "") {
                                array_push($search_records, (object) ['link'=>$link,'word' => $search_world]);
                            }
                        }
                    }
                    // return response()->json('Partners text is Not found', 404);
                }
                // return $search_records;
                $this->createFile($search_records,$affiliate_key);
                    return response()->json(['data'=> $search_records] ,201);

            }
            */


            
            /*
            $selectSql = " select m.brands, t.text_id from `partners_joinpgm` jp 
            left join `partners_merchant` m on jp.`joinpgm_merchantid` = m.`merchant_id` and m.`merchant_status` = 'approved'
            left join `partners_program` p on jp.`joinpgm_programid` = p.`program_id` and p.`program_status` = 'active'
            left join `partners_text_old` t on p.`program_id` = t.`text_programid` and t.`text_status` = 'active'
            where jp.joinpgm_affiliateid = ? and jp.joinpgm_status = 'approved' ";
            */

            $selectSql = " SELECT GROUP_CONCAT(categoryid) as categories FROM `partners_affiliate_categories` where affiliateid = ? ";
            $affiliateCategoriesData = DB::select( DB::raw($selectSql), [$partner_affliate->affiliate_id]);

            $affiliateCategories = "";

            if($affiliateCategoriesData)
            {
                $affiliateCategories = $affiliateCategoriesData[0]->categories;
            }


            $selectSql = " SELECT GROUP_CONCAT(countryid) as countries FROM `partners_affiliate_countries` where affiliateid = ? ";
            $affiliateCountriesData = DB::select( DB::raw($selectSql), [$partner_affliate->affiliate_id]);

            $affiliateCountries = "";

            if($affiliateCountriesData)
            {
                $affiliateCountries = $affiliateCountriesData[0]->countries;
            }

            $mselectSql = " SELECT GROUP_CONCAT(merchant_id) as merchantids FROM `partners_merchant` pm left join `partners_merchant_countries` pmc on pmc.merchantid = pm.merchant_id where merchant_category in ($affiliateCategories) and pmc.countryid in ($affiliateCountries) ";
            $merchantIdsData = DB::select( DB::raw($mselectSql));

            $merchantIds = "";

            if($merchantIdsData)
            {
                $merchantIds = $merchantIdsData[0]->merchantids;
            }

            $selectSql = " select m.brands, t.text_id from `partners_joinpgm` jp left join `partners_merchant` m on jp.`joinpgm_merchantid` = m.`merchant_id` left join `partners_program` p on jp.`joinpgm_programid` = p.`program_id` left join `partners_text_old` t on p.`program_id` = t.`text_programid` where jp.joinpgm_affiliateid = ? and jp.joinpgm_status = 'approved' and m.`merchant_status` = 'approved' and p.`program_status` = 'active' and t.`text_status` = 'active' and m.`merchant_id` in ($merchantIds) order by m.brand_power asc ";

            $partner_join_pgms = DB::select( DB::raw($selectSql), [$partner_affliate->affiliate_id]);

            // dd($partner_join_pgms);

            //  check if partner programs is not null
            if($partner_join_pgms ){

                $usedWords = [];

                $links =[];
                $search_worlds =[];
                $search_records =[];
                foreach($partner_join_pgms as $partner_join_pgm){
                    // we go to the partners_text_old table to get the link
                    // to check the text_status is active
                    //if(!is_null($partner_join_pgm->program->text)){

                        //if ($partner_join_pgm->program->text->text_status  =='active') {
                            // $link =  $partner_join_pgm->program->text->text_url;
                            $link = config('app.api_url')."/saveDetail?id=".$partner_join_pgm->text_id.'&key='.$affiliate_key;
                            // array_push($links, $new_link);
                            $search_world =  $partner_join_pgm->brands;

                            if ($search_world !=="") {

                                $brandKeywords = explode("|", $search_world);

                                if(count($brandKeywords) > 1)
                                {
                                    foreach($brandKeywords as $brandKeyword)
                                    {
                                        if(!in_array($brandKeyword, $usedWords))
                                        {
                                            array_push($search_records, (object) ['i'=>$partner_join_pgm->text_id,'w' => $brandKeyword]);  
                                            
                                            $usedWords[] = $brandKeyword;
                                        }
                                    }
                                }
                                else{
                                    if(!in_array($search_world, $usedWords))
                                    {
                                        array_push($search_records, (object) ['i'=>$partner_join_pgm->text_id,'w' => $search_world]);

                                        $usedWords[] = $search_world;
                                    }
                                }
                            }
                        //}
                    //}
                    // return response()->json('Partners text is Not found', 404);
                }
                // return $search_records;
                $this->createFile($search_records,$affiliate_key);
                    return response()->json([
                        'data'=> $search_records, 
                        'override' => $partner_affliate->link_override_value, 
                        'trackid' => $partners_poweredwords_tracking->id, 
                        //'merchantIds' => $merchantIds,
                        //'affiliateCategories' => $affiliateCategories,
                        //'affiliateCountries' => $affiliateCountries,
                        'mselectSql' => $mselectSql,
                        //"merchantIdsData" => $merchantIdsData,
                        'selectSql' => $selectSql
                        ] ,201);

            }

            



            return response()->json('Partners Pgm is Not Active', 404);
        } else {
            return response()->json('Token Error', 404);
        }
    }


    public function getPartnersNew(Request $request)
    {
        
        $refereruri = $request->header('Current-Page');
        $affiliate_key = $request->header('Api-Key');
        // to check this key belongs to which program affliate
        $multiWhere = array('affiliate_secretkey' => $affiliate_key, 'affiliate_status' => "approved" );
        //$partner_affliate = PartnersAffiliate::where('affiliate_secretkey',$affiliate_key)->first();

        $partner_affliate = PartnersAffiliate::where($multiWhere)->first();
        if ($partner_affliate) {

            try{
                $partners_poweredwords_tracking = PartnersPoweredWordsTracking::create([
                    'affiliateid' => $partner_affliate->affiliate_id,
                    'domain' => $refereruri,
                ]);
            }
            catch(Exception $e)
            {
                
            }

            $multiWhere = array('pageurl' => $refereruri, 'affiliateid' => $partner_affliate->affiliate_id );

            $partnerAffliatePage = PartnersAffiliatePages::where($multiWhere)->first();
    

            // check if cache file exists for partner for today date  --affiliate_secretkey-af
            //  i.e  current date-1a2v3a4z5c6h7a8t9b0ots2c5ri00pt-af
            //  if file exists return json from file
            //  if file does not exists run process create a new cache file for today date and return response
            
            $filename = $affiliate_key.'--'.now()->toDateString().'-af.json';

            if($partnerAffliatePage != null && $partnerAffliatePage->categoryid > 0)
            {
                $filename = $affiliate_key.'--'.$partnerAffliatePage->id.'--'.$partnerAffliatePage->categoryid.'--'.now()->toDateString().'-af.json';
            }
           
            
            if (file_exists( public_path()."/affiliateFile/".$filename)){
                try
                {
                    $contents = File::get(public_path()."/affiliateFile/".$filename);

                    return response()->json(['referrer' => $refereruri,'affiliateid' => $partner_affliate->affiliate_id, 'url' => $partnerAffliatePage, 'data'=> json_decode($contents),'override' => $partner_affliate->link_override_value, 'trackid' => $partners_poweredwords_tracking->id ] ,201);

                }
                catch (Illuminate\Contracts\Filesystem\FileNotFoundException $exception)
                {
                    return response()->json('File text is Not Readable form', 404);
                }
            }
         
            $selectSql = " SELECT GROUP_CONCAT(categoryid) as categories FROM `partners_affiliate_categories` where affiliateid = ? ";
            $affiliateCategoriesData = DB::select( DB::raw($selectSql), [$partner_affliate->affiliate_id]);

            $affiliateCategories = "";

            if($affiliateCategoriesData)
            {
                $affiliateCategories = $affiliateCategoriesData[0]->categories;
            }


            $selectSql = " SELECT GROUP_CONCAT(countryid) as countries FROM `partners_affiliate_countries` where affiliateid = ? ";
            $affiliateCountriesData = DB::select( DB::raw($selectSql), [$partner_affliate->affiliate_id]);

            $affiliateCountries = "";

            if($affiliateCountriesData)
            {
                $affiliateCountries = $affiliateCountriesData[0]->countries;
            }

            if($partnerAffliatePage != null && $partnerAffliatePage->categoryid > 0)
            {
                $affiliateCategories = $partnerAffliatePage->categoryid;
            }
            
            $mselectSql = " SELECT GROUP_CONCAT(merchant_id) as merchantids FROM `partners_merchant` pm left join `partners_merchant_countries` pmc on pmc.merchantid = pm.merchant_id where merchant_category in ($affiliateCategories) and pmc.countryid in ($affiliateCountries) ";
            
            
            $merchantIdsData = DB::select( DB::raw($mselectSql));

            $merchantIds = "";

            if($merchantIdsData)
            {
                $merchantIds = $merchantIdsData[0]->merchantids;
            }

            $usedWords = [];
            $search_records =[];
            
            $selectSql = " SELECT b.id as bidid, k.keyword FROM `partners_keyword_bids` b left join partners_keywords k on b.keywordid = k.id WHERE b.status = 1 and b.merchantid in ($merchantIds) ";


            $merchant_keywords = DB::select(DB::raw($selectSql));

            if($merchant_keywords)
            {
                foreach($merchant_keywords as $merchant_keyword){

                    array_push($search_records, (object) ['i'=>$merchant_keyword->bidid,'w' => $merchant_keyword->keyword, 't' => "pw"]);  
                    $usedWords[] = $merchant_keyword->keyword;
                }
            }

            $selectSql = " select m.brands, t.text_id from `partners_joinpgm` jp left join `partners_merchant` m on jp.`joinpgm_merchantid` = m.`merchant_id` left join `partners_program` p on jp.`joinpgm_programid` = p.`program_id` left join `partners_text_old` t on p.`program_id` = t.`text_programid` where jp.joinpgm_affiliateid = ? and jp.joinpgm_status = 'approved' and m.`merchant_status` = 'approved' and p.`program_status` = 'active' and t.`text_status` = 'active' and m.`merchant_id` in ($merchantIds) order by m.brand_power asc ";

            $partner_join_pgms = DB::select( DB::raw($selectSql), [$partner_affliate->affiliate_id]);

            // dd($partner_join_pgms);

            //  check if partner programs is not null
            if($partner_join_pgms ){

                

                $links =[];
                $search_worlds =[];
                
                foreach($partner_join_pgms as $partner_join_pgm){
                    // we go to the partners_text_old table to get the link
                    // to check the text_status is active
                    //if(!is_null($partner_join_pgm->program->text)){

                        //if ($partner_join_pgm->program->text->text_status  =='active') {
                            // $link =  $partner_join_pgm->program->text->text_url;
                            $link = config('app.api_url')."/saveDetail?id=".$partner_join_pgm->text_id.'&key='.$affiliate_key;
                            // array_push($links, $new_link);
                            $search_world =  $partner_join_pgm->brands;

                            if ($search_world !=="") {

                                $brandKeywords = explode("|", $search_world);

                                if(count($brandKeywords) > 1)
                                {
                                    foreach($brandKeywords as $brandKeyword)
                                    {
                                        if(!in_array($brandKeyword, $usedWords))
                                        {
                                            array_push($search_records, (object) ['i'=>$partner_join_pgm->text_id,'w' => $brandKeyword]);  
                                            
                                            $usedWords[] = $brandKeyword;
                                        }
                                    }
                                }
                                else{
                                    if(!in_array($search_world, $usedWords))
                                    {
                                        array_push($search_records, (object) ['i'=>$partner_join_pgm->text_id,'w' => $search_world]);

                                        $usedWords[] = $search_world;
                                    }
                                }
                            }
                        //}
                    //}
                    // return response()->json('Partners text is Not found', 404);
                }
                // return $search_records;
                $this->createFile($search_records, $affiliate_key, $filename);
                    return response()->json([
                        'url' => $partnerAffliatePage,
                        'data'=> $search_records, 
                        'override' => $partner_affliate->link_override_value, 
                        'trackid' => $partners_poweredwords_tracking->id, 
                        //'merchantIds' => $merchantIds,
                        //'affiliateCategories' => $affiliateCategories,
                        //'affiliateCountries' => $affiliateCountries,
                        'mselectSql' => $mselectSql,
                        //"merchantIdsData" => $merchantIdsData,
                        'selectSql' => $selectSql
                        ] ,201);

            }

            



            return response()->json('Partners Pgm is Not Active', 404);
        } else {
            return response()->json('Token Error', 404);
        }
    }

    public function getTokens(Request $request)
    {
        // Fetch all  verify_tokens
        $data = VerifyToken::all();
        return response()->json(['Partners Merchant', 'data' => $data, 201]);
    }

    public function saveDetail(Request $request)
    {
        // take the ip and link
        $user_ip = $request->ip();
        // text old table

        $partner_affliate = PartnersAffiliate::where('affiliate_secretkey',$request->key)->first();

        $text_id = null;
        $aid = null;

        $redirectParams = "";

        if(isset(request()->type) && request()->type == "pw")
        {

            $selectSql = " SELECT * FROM `partners_keyword_bids` where id =  ? ";
            $partnerKeywordBid = DB::select( DB::raw($selectSql), [request()->id]);

            if($partnerKeywordBid)
            {
            	
            	$redirectParams = "&clicksource=poweredwords&bidid=".$partnerKeywordBid[0]->id;
                $merchantid = $partnerKeywordBid[0]->merchantid;

                /*
                $selectSql = " SELECT p.*, pjp.joinpgm_id FROM `partners_program` p join partners_text_old pto on p.program_id = pto.text_programid join partners_joinpgm pjp on p.program_id = pjp.joinpgm_programid WHERE program_merchantid = ".$merchantid." and pjp.joinpgm_merchantid = ".$merchantid." and pjp.joinpgm_affiliateid = ".$partner_affliate->affiliate_id." ORDER by program_id desc ";*/

                $selectSql = " SELECT p.* FROM `partners_program` p join partners_text_old pto on p.program_id = pto.text_programid WHERE program_merchantid = ".$merchantid." ORDER by program_id desc; ";

                $partnerProgram = DB::select( DB::raw($selectSql));

                if($partnerProgram)
                {
                    $program_id = $partnerProgram[0]->program_id ;

                    $text_old = PartnerTextOld::where('text_programid',$program_id)->first();

                    $partner_url = $text_old->text_url;
                    $text_id = $text_old->text_id;
                }
            }
        }
        else
        {
            $text_old = PartnerTextOld::where('text_id',request()->id)->first();
            $partner_url = $text_old->text_url;
            $program_id = $text_old->text_programid;
            $text_id = $text_old->text_id;

            // get  partner_program table
            $program = PartnerProgram::where('program_id',$program_id)->first();

              
        }


//  to get the partnerjoinpgm table record
            $partner_join_pgm = PartnersJoinPgm::where('joinpgm_programid',$program_id)
            // ->where('joinpgm_merchantid', $program->program_merchantid)
            ->first();
             if(is_null($partner_join_pgm)){
                return response()->json(['messgage'=>'No JoinPgm  Found', 'data' => [], 400]);
             } 
      
        // to check this key belongs to which program affliate
        

        if($partner_affliate->count()  >0){
            // table name partners_owner_auid_ip
            // save the record againist the record

            $data = PartnerIpStat::create([
                'ownerid'=>	$partner_affliate->affiliate_id,
                'AUIDS'=>'',
                'ipaddress'=> $user_ip, 
            ]);         
             
            $aid = $partner_affliate->affiliate_id;
            
            /*
            // dd( $program->program_merchantid,$program_id);
            $partners_transaction=PartnersTranscation::create([
            'transaction_dateoftransaction' => now()->toDateString(),
            'transaction_dateofpayment' =>'0000-00-00',
            'transaction_adminpaydate' =>'0000-00-00',
            'transaction_subsaledate' => '0000-00-00',
            'transaction_reversedate' =>'0000-00-00',
            'transaction_transactiontime' => now()->format('Y-m-d H:i:s'),
            'transaction_joinpgmid' => $partner_join_pgm->joinpgm_id, 
            'transaction_type' =>'click',
            'transaction_status' =>'pending',
            'transaction_orderid' =>'',
            'transaction_country' =>'',
            'transaction_subid' =>'',
            'aiapproved' =>0,
            'aiscore' =>0,
            // 'transaction_linkid' =>,
            'transaction_referer' =>$request->url(),
            'transaction_ip' =>$request->ip(),
            ]);

            $new_link = str_replace('{CLICKID}', 'PW-'.$partners_transaction->id, $partner_url);
            return  redirect($new_link);
            */

            $new_link = "https://performanceaffiliate.com/trackingcode.php?aid=".$aid."&linkid=N".$text_id.$redirectParams;
            //die($new_link);
            return  redirect($new_link);
        }

        return response()->json(['messgage'=>'No key Found', 'data' => [], 400]);
    }

    public function countClick(Request $request)
    {   

        // text old table
        $text_old = PartnerTextOld::where('text_id',request()->id)->first();

        //  replace the
        $new_link = str_replace('{CLICKID}', 'LX-', $text_old->text_url);
        $program_id = $text_old->text_programid;
        
        // get  partner_program table
        $program = PartnerProgram::where('program_id',$program_id)->first();

        //  to get the partnerjoinpgm table record
        $partner_join_pgm = PartnersJoinPgm::where('joinpgm_programid',$program_id)
        ->where('joinpgm_merchantid', $program->program_merchantid)
        ->first();


        $partners_transaction=PartnersTranscation::create([
            'transaction_dateoftransaction' =>now()->format('y-m-d'),
            'transaction_transactiontime' =>now()->format('H:i:s'),
            'transaction_joinpgmid' => $partner_join_pgm->joinpgm_id, 
            'transaction_type' =>'click',
            'transaction_status' =>'pending',
            // 'transaction_linkid' =>,
            'transaction_referer' =>$request->url(),
            'transaction_ip' =>$request->ip(),
        ]);


        return redirect($text_old->text_url);
    }

    public function createFile($data, $key, $filename = ""){

        $dirPath = public_path()."/affiliateFile/";
        $jsonFiles = scandir($dirPath, 1);

        $today = now()->toDateString();

        foreach($jsonFiles as $file)
        {
            
            if(strpos($file, $key) !== false && strpos($file, $today) == false)
            {
                File::delete($dirPath.$file);
            }
        }
        
        //  creating new file
        $data = json_encode($data);
        $file = $key.'--'.now()->toDateString().'-af.json';

        if($filename != "")
        {
            $file = $filename;
        }


        $destinationPath=public_path()."/affiliateFile/";
        if (!is_dir($destinationPath)) {  mkdir($destinationPath,0777,true);  }
        File::put($destinationPath.$file,$data);
        return response()->download($destinationPath.$file);
      }

      public function trackMatchedWords(Request $request)
      {
            $status = false;
            $trackid = $request->input('trackid');
            $matched = $request->input('matched');

            if($matched != "")
            {
                $matched = json_decode($matched);

                foreach($matched as $match)
                {
                    try{
                        $partners_poweredwords_match = PartnersPoweredWordsMatch::create([
                            'trackid' => $trackid,
                            'word' => $match->word,
                            'matchcount' => $match->count,
                            'keywordid' => isset($match->id) ? $match->id : null
                        ]);
                    }
                    catch(Exception $e)
                    {
                        
                    }
                }

            }

            return response()->json([$status => $status]);
      }

      public function trackErrorWords(Request $request)
      {
            $status = false;
            $trackid = $request->input('trackid');
            $errors = $request->input('errors');

            if($errors != "")
            {
                $errors = json_decode($errors);

                foreach($errors as $error)
                {
                    try{
                        $partners_poweredwords_match = PartnersPoweredWordsMatch::create([
                            'trackid' => $trackid,
                            'word' => $error->word,
                            'iserror' => 1
                        ]);
                    }
                    catch(Exception $e)
                    {
                        
                    }
                }

            }

            return response()->json([$status => $status]);
      }


      public function wordMatchStats(Request $request)
      {
            $matchedStats = null;
            $status = false;
            $word = $request->input('word');

            if($word != "")
            {
                 $selectSql = " SELECT DATE(datetime) as date, sum(matchcount) matchcount FROM `partners_poweredwords_tracking` pwt left join partners_poweredwords_match pwm on pwt.id = pwm.trackid where pwm.word = '".$word."' group by date order by date desc; ";

                $wordMatchStats = DB::select( DB::raw($selectSql));

                if($wordMatchStats)
                {
                    foreach($wordMatchStats as $stats){
                        $matchedStats[] = $stats;
                    }
                    
                    $status = true;
                }

            }

            return response()->json(['status' => $status, 'data' => $matchedStats]);
      }

      //;

}
