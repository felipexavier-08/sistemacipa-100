<?php

    class Funcionario {
        
        private int $idFuncionario;
        private string $nomeFuncionario;
        private string $sobrenomeFuncionario;
        private string $cpfFuncionario;
        private string $dataNascimentoFuncionario;
        private string $dataContratacaoFuncionario;
        private string $telefoneFuncionario;
        private string $matriculaFuncionario;
        private string $codigoVotoFuncionario;
        private int $ativoFuncionario;
        private int $admFuncionario;
        private string $emailFuncionario;
        private string $senhaFuncionario;
        private bool $jaVotou = false; // Status de voto na eleição ativa

        public function __construct(
            string $nomeFuncionario,
            string $sobrenomeFuncionario,
            string $cpfFuncionario,
            string $dataNascimentoFuncionario,
            string $dataContratacaoFuncionario,
            string $telefoneFuncionario,
            string $matriculaFuncionario,
            int $ativoFuncionario,
            int $admFuncionario,
            string $emailFuncionario,
            string $senhaFuncionario,
            string $codigoVotoFuncionario = '', // Código de voto opcional
            int $idFuncionario = 0
        ) {
            $this->idFuncionario = $idFuncionario;
            $this->nomeFuncionario = $nomeFuncionario;
            $this->sobrenomeFuncionario = $sobrenomeFuncionario;
            $this->cpfFuncionario = $cpfFuncionario;
            $this->dataNascimentoFuncionario = $dataNascimentoFuncionario;
            $this->dataContratacaoFuncionario = $dataContratacaoFuncionario;
            $this->telefoneFuncionario = $telefoneFuncionario;
            $this->matriculaFuncionario = $matriculaFuncionario;
            $this->codigoVotoFuncionario = $codigoVotoFuncionario;
            $this->ativoFuncionario = $ativoFuncionario;
            $this->admFuncionario = $admFuncionario;
            $this->emailFuncionario = $emailFuncionario;
            $this->senhaFuncionario = $senhaFuncionario;
        }

        // Getters
        public function getIdFuncionario() { return $this->idFuncionario; }
        public function getNomeFuncionario() { return $this->nomeFuncionario; }
        public function getSobrenomeFuncionario() { return $this->sobrenomeFuncionario; }
        public function getCpfFuncionario() { return $this->cpfFuncionario; }
        public function getDataNascimentoFuncionario() { return $this->dataNascimentoFuncionario; }
        public function getDataContratacaoFuncionario() { return $this->dataContratacaoFuncionario; }
        public function getTelefoneFuncionario() { return $this->telefoneFuncionario; }
        public function getMatriculaFuncionario() { return $this->matriculaFuncionario; }
        public function getCodigoVotoFuncionario() { return $this->codigoVotoFuncionario; }
        public function getAtivoFuncionario() { return $this->ativoFuncionario; }
        public function getAdmFuncionario() { return $this->admFuncionario; }
        public function getEmailFuncionario() { return $this->emailFuncionario; }
        public function getSenhaFuncionario() { return $this->senhaFuncionario; }
        public function getJaVotou() { return $this->jaVotou; }
        public function setJaVotou($jaVotou) { $this->jaVotou = $jaVotou; }
      
    }

?>