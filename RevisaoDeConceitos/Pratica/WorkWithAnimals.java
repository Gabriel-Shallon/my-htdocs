package Pratica;

import javax.swing.JOptionPane;

public class WorkWithAnimals {
    public static void main(String[] args){


        
        Dog bobTheDog = new Dog();



        bobTheDog.setName(JOptionPane.showInputDialog(null, "What'z da dogs naime??"));


        bobTheDog.setWeight(Integer.parseInt(JOptionPane.showInputDialog(null, "What'z da dogs weight??")));


        bobTheDog.setHeight(Double.parseDouble(JOptionPane.showInputDialog(null, "What'z da dogs height??")));







        

    }
}
