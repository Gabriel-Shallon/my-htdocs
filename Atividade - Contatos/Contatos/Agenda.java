package Contatos;

import java.util.ArrayList;

import javax.swing.JOptionPane;

public class Agenda {
    public static void main(String[] args){

        ArrayList<Contato> listaDeContatos;
        Object[] options = {"Cadastrar Contato","Buscar Contato", "Excluir Contato", "Imprimir Agenda", "Sair"};



        int opt = JOptionPane.showOptionDialog(null, 
                                               "Opções:", 
                                               "Menu", 
                                               JOptionPane.DEFAULT_OPTION, 
                                               JOptionPane.INFORMATION_MESSAGE,
                                               null, 
                                               options, 
                                               options[0]);
        


    }
}
