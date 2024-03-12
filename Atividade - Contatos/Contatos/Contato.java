package Contatos;

    class Contato{

        private String nome;
        private String telefone;

        Contato(String nome, String telefone){
            this.nome = nome;
            this.telefone = telefone;
        }



        public void setNome(String newNome){
            nome = newNome;
        } 

        public void setTelefone(String newTelefone){
            telefone = newTelefone;
        } 



        public String setNome(){
            return nome;
        } 

        public String setTelefone(){
            return telefone;
        }
        


    }