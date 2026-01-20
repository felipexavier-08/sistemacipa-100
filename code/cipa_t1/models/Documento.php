<?php

    class Documento{

        private int $idDocumento;
        private string $dataHoraDocumento;
        private string $dataInicioDocumento;
        private string $dataFimDocumento;
        private string $pdfDocumento;
        private string $tituloDocumento;
        private string $tipoDocumento;
        private string $statusEleicaoVinculada;

        public function __construct(
            string $dataInicioDocumento,
            string $dataFimDocumento,
            string $tituloDocumento,
            string $tipoDocumento,
            string $pdfDocumento = "",
            string $dataHoraDocumento = "",
            int $idDocumento = 0,
            string $statusEleicaoVinculada = "SEM_VINCULO"
        ){
            $this->idDocumento = $idDocumento;
            $this->dataHoraDocumento = $dataHoraDocumento;
            $this->dataInicioDocumento = $dataInicioDocumento;
            $this->dataFimDocumento = $dataFimDocumento;
            $this->pdfDocumento = $pdfDocumento;
            $this->tituloDocumento = $tituloDocumento;
            $this->tipoDocumento = $tipoDocumento;
            $this->statusEleicaoVinculada = $statusEleicaoVinculada;
    
            
        }
        public function getIdDocumento() {return $this->idDocumento;}
        public function getDataHoraDocumento() {return $this->dataHoraDocumento;}
        public function getDataInicioDocumento() {return $this->dataInicioDocumento;}
        public function getDataFimDocumento() {return $this->dataFimDocumento;}
        public function getPdfDocumento() {return $this->pdfDocumento;}
        public function getTituloDocumento() {return $this->tituloDocumento;}
        public function getTipoDocumento() {return $this->tipoDocumento;}
        public function getStatusEleicaoVinculada() {return $this->statusEleicaoVinculada;}
    }


?>