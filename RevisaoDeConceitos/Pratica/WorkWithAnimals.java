package Pratica;

import javax.swing.JOptionPane;

public class WorkWithAnimals {
    public static void main(String[] args){


       int AnimalType = JOptionPane.showConfirmDialog(null, "Qual animal irá registrar?");
       String Animal;

        if (AnimalType == 0) {

             Animal = "Cachorro";

        Dog Animal1 = new Dog((JOptionPane.showInputDialog(null, "Qual o nome do(a) "+Animal+"?")),
        (Double.parseDouble(JOptionPane.showInputDialog(null, "Qual a altura do(a) "+Animal+"?"))),
        (Integer.parseInt(JOptionPane.showInputDialog(null, "Qual o peso do(a) "+Animal+"?"))));


        }else if(AnimalType == 1) { 
             
            Animal = "Gato";
        
        Cat Animal1 = new Cat((JOptionPane.showInputDialog(null, "Qual o nome do(a) "+Animal+"?")),
        (Double.parseDouble(JOptionPane.showInputDialog(null, "Qual a altura do(a) "+Animal+"?"))),
        (Integer.parseInt(JOptionPane.showInputDialog(null, "Qual o peso do(a) "+Animal+"?"))));

        }else{ 
            
            Animal = "Pássaro";
        
        Bird Animal1 = new Bird((JOptionPane.showInputDialog(null, "Qual o nome do(a) "+Animal+"?")),
        (Double.parseDouble(JOptionPane.showInputDialog(null, "Qual a altura do(a) "+Animal+"?"))),
        (Integer.parseInt(JOptionPane.showInputDialog(null, "Qual o peso do(a) "+Animal+"?"))));

        }

        

    }
}
