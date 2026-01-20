<?php

    require_once __DIR__ . "/Funcionario.php";
    require_once __DIR__ . "/Documento.php";

    class Candidato {
        private int $idCandidato;
        private ?Funcionario $funcionarioFK;
        private string $fotoCandidato;
        private string $numeroCandidato;
        private string $cargoCandidato;
        private string $dataRegistroCandidato;
        private ?Documento $eleicaoFK;
        private string $statusCandidatoAta;
        private int $quantidadeVotoCandidato;

        public function __construct(
            string $fotoCandidato,
            string $numeroCandidato,
            string $cargoCandidato,
            string $statusCandidatoAta = "ATIVO",
            string $dataRegistroCandidato = "",
            ?Documento $eleicaoFK = null,
            ?Funcionario $funcionarioFK = null,
            int $quantidadeVotoCandidato = 0,
            int $idCandidato = 0
        ) {
            $this->idCandidato = $idCandidato;
            $this->funcionarioFK = $funcionarioFK;
            $this->fotoCandidato = $fotoCandidato;
            $this->numeroCandidato = $numeroCandidato;
            $this->cargoCandidato = $cargoCandidato;
            $this->dataRegistroCandidato = $dataRegistroCandidato;
            $this->eleicaoFK = $eleicaoFK;
            $this->statusCandidatoAta = $statusCandidatoAta;
            $this->quantidadeVotoCandidato = $quantidadeVotoCandidato;
        }

        public function getIdCandidato() { return $this->idCandidato; } 
        public function getFuncionarioFK(): Funcionario { return $this->funcionarioFK; } 
        public function getFotoCandidato() { return $this->fotoCandidato; } 
        public function getNumeroCandidato() { return $this->numeroCandidato; } 
        public function getCargoCandidato() { return $this->cargoCandidato; } 
        public function getDataRegistroCandidato() { return $this->dataRegistroCandidato; } 
        public function getEleicaoFK(): Documento { return $this->eleicaoFK; } 
        public function getStatusCandidatoAta() { return $this->statusCandidatoAta; } 
        public function getQuantidadeVotoCandidato() { return $this->quantidadeVotoCandidato; } 
    }

?>