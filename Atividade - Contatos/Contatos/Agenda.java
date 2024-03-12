package Contatos;

import java.util.ArrayList;
import javax.swing.JOptionPane;
import javax.swing.JTextField;
import javax.swing.UIManager;

public class Agenda {
    public static void main(String[] args){

        ArrayList<Contato> listaDeContatos = new ArrayList<>();
        Object[] options = {"Cadastrar Contato","Buscar Contato", "Excluir Contato", "Imprimir Agenda", "Sair"};
        int opt=5;




    while (opt != 4){

        
    if (opt == 0){

                JTextField nomeField = new JTextField(2);
                JTextField telefoneField = new JTextField(2);

        Object[] message = {
            "Nome:", nomeField,
            "Telefone:", telefoneField
        };

            UIManager.put("OptionPane.cancelButtonText","Cancelar");
            UIManager.put("OptionPane.okButtonText","Cadastrar");


            int option = JOptionPane.showConfirmDialog(null, message, "Entre com as informações", JOptionPane.OK_CANCEL_OPTION);

            if (option == JOptionPane.OK_OPTION) {
                Contato novoContato = new Contato(nomeField.getText(), telefoneField.getText());
                listaDeContatos.add(novoContato);
            }
      

        }else if(opt == 1){



        }else if(opt == 2){



        }else if(opt == 3){



        }



        opt = JOptionPane.showOptionDialog(null, "Opções:", "Menu", JOptionPane.DEFAULT_OPTION, JOptionPane.INFORMATION_MESSAGE,null, options, options[0]);

    }
    }
}
