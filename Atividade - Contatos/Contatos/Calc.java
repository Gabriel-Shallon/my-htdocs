package Contatos;

import java.util.ArrayList;
import java.util.Iterator;
import javax.swing.JOptionPane;
import javax.swing.JTextField;
import javax.swing.UIManager;

public class Calc {


    public void calc(){
        
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

        UIManager.put("OptionPane.okButtonText","Buscar");

          String search = JOptionPane.showInputDialog(null, "Buscar número de contato pelo nome:");

        UIManager.put("OptionPane.okButtonText","Ok");

          int flag = 0;
          for (Contato contato : listaDeContatos) {
            if (contato.getNome().equals(search)) {
                JOptionPane.showMessageDialog(null, "Nome: "+contato.getNome()+"\nNúmero: "+contato.getTelefone());
                flag = 1;
            }
          }
          
          if (flag==0){

            JOptionPane.showMessageDialog(null, "Nenhum resultado encontrado.");

          }





        }else if(opt == 2){

        UIManager.put("OptionPane.okButtonText","Excluir");

            String search = JOptionPane.showInputDialog(null, "Digite o nome do contato que deseja excluir:");
  
        UIManager.put("OptionPane.okButtonText","Ok");
  
        int flag = 0;
        Iterator<Contato> iterator = listaDeContatos.iterator();

          while (iterator.hasNext()) {
            Contato contato = iterator.next();

              if (contato.getNome().equals(search)) {
                JOptionPane.showMessageDialog(null, contato.getNome()+"( "+contato.getTelefone()+") foi excluído.");
              iterator.remove();

              flag = 1;
            }
          }
            if (flag==0){
  
              JOptionPane.showMessageDialog(null, "Nenhum resultado encontrado.");
  
            }





        }else if(opt == 3){


      UIManager.put("OptionPane.okButtonText","Ok");

          
      if (listaDeContatos.isEmpty()) {
        JOptionPane.showMessageDialog(null, "Nenhum contato foi adicionado ainda.");
        }else{

          StringBuilder list = new StringBuilder();
            for (Contato contato : listaDeContatos) {
                list.append("Nome: ").append(contato.getNome());
                list.append("\nNúmero: ").append(contato.getTelefone());
                list.append("\n\n"); 
            }

            JOptionPane.showMessageDialog(null, "Contatos\n\n"+list);

          }

  

        }



        opt = JOptionPane.showOptionDialog(null, "Opções:", "Menu", JOptionPane.DEFAULT_OPTION, JOptionPane.INFORMATION_MESSAGE,null, options, options[0]);

    }
  }  
}