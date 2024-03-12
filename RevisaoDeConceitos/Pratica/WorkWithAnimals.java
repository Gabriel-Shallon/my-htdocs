package Pratica;

import java.util.ArrayList;
import javax.swing.JOptionPane;
import javax.swing.UIManager;

public class WorkWithAnimals {
    public static void main(String[] args){



        ArrayList<Animal> AnimalList = new ArrayList<Animal>();
        int a = 0;
        while (a!=1){

            UIManager.put("OptionPane.yesButtonText","Cachorro");
            UIManager.put("OptionPane.noButtonText","Gato");  
            UIManager.put("OptionPane.cancelButtonText","Pássaro"); 


            int AnimalType = JOptionPane.showConfirmDialog(null, "Qual animal irá registrar?");
            String Animal;
            Animal AnimalTemp;



            UIManager.put("OptionPane.cancelButtonText","Cancelar");

            if (AnimalType == 0) {
                
                Animal = "Cachorro";

                AnimalTemp = new Dog((JOptionPane.showInputDialog(null, "Qual o nome do(a) "+Animal+"?")),
                (Double.parseDouble(JOptionPane.showInputDialog(null, "Qual a altura do(a) "+Animal+"?"))),
                (Integer.parseInt(JOptionPane.showInputDialog(null, "Qual o peso do(a) "+Animal+"?"))));

            }else if(AnimalType == 1){ 

                Animal = "Gato";

                AnimalTemp = new Cat((JOptionPane.showInputDialog(null, "Qual o nome do(a) "+Animal+"?")),
                (Double.parseDouble(JOptionPane.showInputDialog(null, "Qual a altura do(a) "+Animal+"?"))),
                (Integer.parseInt(JOptionPane.showInputDialog(null, "Qual o peso do(a) "+Animal+"?"))));

            }else{ 
                
                Animal = "Pássaro";

                AnimalTemp = new Bird((JOptionPane.showInputDialog(null, "Qual o nome do(a) "+Animal+"?")),
                (Double.parseDouble(JOptionPane.showInputDialog(null, "Qual a altura do(a) "+Animal+"?"))),
                (Integer.parseInt(JOptionPane.showInputDialog(null, "Qual o peso do(a) "+Animal+"?"))));

            }

            AnimalList.add(AnimalTemp);

            UIManager.put("OptionPane.yesButtonText","Sim");
            UIManager.put("OptionPane.noButtonText","Não");
    
            a = JOptionPane.showConfirmDialog(null, "Deseja Registrar outro animal?","Registrar", JOptionPane.YES_NO_OPTION);
        }



        a = JOptionPane.showConfirmDialog(null, "Abrir lista de animais registrados?","List", JOptionPane.YES_NO_OPTION); 
    
        if (a==0){
            String animals = "";
            for (Animal animal : AnimalList) {
                animals += "Id: "+animal.getId()+" /// Nome: "+animal.getName()+" /// Altura: "+animal.getHeight()+" /// Peso: "+animal.getWeight()+"\n";
            }
                JOptionPane.showMessageDialog(null, animals);
        }
    }
}
