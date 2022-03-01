<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Schema::create('test', function (Blueprint $table) {
            $table -> string('ID_ITEM');
            $table -> string('FECHA_HORA_KM');
            $table -> string('FECHA_Y_HORA');
            $table -> string('LN_TARJ');
            $table -> string('FIID_TARJ');
            $table -> string('NUMERO_TARJETA');
            $table -> string('LN_COMER');
            $table -> string('FIID_COMER');
            $table -> string('ID_COMER');
            $table -> string('TERM_COMER');
            $table -> string('LN_TERM');
            $table -> string('FIID_COMER');
            $table -> string('TIPO');
            $table -> string('R');
            $table -> string('NUM_SEC');
            $table -> string('NOMBRE_DE_TERMINAL');
            $table -> string('TIPO_TRANSAC');
            $table -> string('CTA_ASOCIADA');
            $table -> string('CODIGO_RESPUESTA');
            $table -> string('MONTO1');
            $table -> string('CODIGO_APROV');
            $table -> string('PREFIJO6');
            $table -> string('KC4_TKN_ID');
            $table -> string('KC4_TERM_ATTEND_IND');
            $table -> string('KC4_TERM_OPER_IND');
            $table -> string('KC4_TERM_LOC_IND');
            $table -> string('KC4_CRDHLDR_PRESENT_IND');
            $table -> string('KC4_CRD_PRESENT_IND');
            $table -> string('KC4_CRD_CAPTR_IND	');
            $table -> string('KC4_TXN_STAT_IND');
            $table -> string('KC4_TXN_SEC_IND');
            $table -> string('KC4_TXN_RTN_IND');
            $table -> string('KC4_CRDHLDR_ACTVT_TERM_IND');
            $table -> string('KC4_TERM_INPUT_CAP_IND');
            $table -> string('KC4_CRDHLDR_ID_METHOD');
            $table -> string('KC0_IDENTIFICADOR_DEL_TOKEN');
            $table -> string('KC0_LONGITUD_DE_DATOS');
            $table -> string('KC0_INDICADOR_DE_COMERCIO_ELEC');
            $table -> string('KC0_TIPO_DE_TARJETA');
            $table -> string('KC0_INDICADOR_DE_INFORMACION_A');
            $table -> string('KC0_INDICADOR_DE_CVV2_CVC2_PRE');
            $table -> string('KB3_TOKEN_ID');
            $table -> string('KB3_BIT_MAP');
            $table -> string('KB3_TERM_SRL_NUM');
            $table -> string('KB3_EMV_TERM_CAP');
            $table -> string('KB3_USR_FLD1');
            $table -> string('KB3_USR_FLD2');
            $table -> string('KB3_EMV_TERM_TYPE');
            $table -> string('KB3_APP_VER_NUM');
            $table -> string('KB3_CVM_RSLTS');
            $table -> string('KB3_DF_NAME_LGTH');
            $table -> string('KB3_DF_NAME');
            $table -> string('KB4_ID_TOKEN');
            $table -> string('KB4_PT_SRV_ENTRY_MDE');
            $table -> string('KB4_TERM_ENTRY_CAP');
            $table -> string('KB4_LAST_EMV_STAT');
            $table -> string('KB4_DATA_SUSPECT');
            $table -> string('KB4_APPL_PAN_SEQ_NUM');
            $table -> string('KB4_DEV_INFO');
            $table -> string('KB4_RSN_ONL_CDE');
            $table -> string('KB4_ARQC_VRFY');
            $table -> string('KB4_ISO_RC_IND');
            $table -> string('KB2_TOKEN_ID');
            $table -> string('KB2_BIT_MAP');
            $table -> string('KB2_USR_FLD1');
            $table -> string('KB2_CRYPTO_INFO_DATA');
            $table -> string('KB2_TVR');
            $table -> string('KB2_ARQC');
            $table -> string('KB2_AMT_AUTH');
            $table -> string('KB2_AMT_OTHER');
            $table -> string('KB2_AIP');
            $table -> string('KB2_ATC');
            $table -> string('KB2_TERM_CTRY_CDE');
            $table -> string('KB2_TRAN_CRNCY_CDE');
            $table -> string('KB2_TRAN_TYPE');
            $table -> string('KB2_UNPREDICT_NUM');
            $table -> string('KB2_ISS_APPL_DATA_LGTH');
            $table -> string('KB2_ISS_APPL_DATA');
            $table -> string('KQ2_ID_MEDIO_ACCESO');
            $table -> string('ENTRY_MODE');
            

        });
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test');
    }
};
