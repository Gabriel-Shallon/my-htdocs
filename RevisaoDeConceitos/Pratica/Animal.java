package Pratica;
import javax.swing.JOptionPane;


class Animal{

    int nextId = 1;

    private int id;
    private String name;
    private double height;
    private int weight;


    Animal(String name, double height, int weight){
        this.id = nextId++;
        this.name = name;
        this.height = height;
        this.weight = weight;
    }


    public void setName(String newName){
        
        if (name.length()<2){

            JOptionPane.showMessageDialog(null, "O nome deve conter pelo menos 2 letras.");

        }else{

        name = newName;
        
        }
    }

    public void setHeight(double newHeight){
    
        if (height<=0){

            JOptionPane.showMessageDialog(null, "A altura do animal não pode ser menor ou igual a zero.");

        }else{

        height = newHeight;

        }
    
    }

    public void setWeight(int newWeight){
    
        if (weight<=0){

            JOptionPane.showMessageDialog(null, "O peso do animal não pode ser menor ou igual a zero.");

        }else{

        weight = newWeight;

        }
    
    }


    public void setSound(String string) {
        
    }



    public int getId(){
    
        return id;
    
    }


    public String getName(){
    
        return name;
    
    }

    public double getHeight(){
    
        return height;
    
    }

    public int getWeight(){
    
        return weight;
    
    }


}
